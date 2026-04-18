#!/usr/bin/env node
/**
 * One-shot build script — regenerates resources/svg/iraq-map.svg from the
 * Natural Earth 1:10m vector dataset (public domain, Mike Bostock / D3 +
 * Nathan Kelso). Output is an editorial-grade cartographic SVG:
 *
 *   • Iraq outline          — 1:10m countries, simplified to 0.15 SVG units
 *   • Neighboring countries — Turkey, Iran, Syria, Jordan, Saudi Arabia,
 *                             Kuwait — rendered as hair-line borders so
 *                             Iraq sits in geographic context, not a void
 *   • Tigris + Euphrates    — real river centerlines (ne_10m_rivers), not
 *                             hand-drawn Béziers
 *
 * Sources:
 *   Countries : https://unpkg.com/world-atlas@2/countries-10m.json
 *   Rivers    : https://raw.githubusercontent.com/nvkelso/natural-earth-vector/master/geojson/ne_10m_rivers_lake_centerlines.geojson
 *
 * Projection : linear lat/lng → SVG mapping, MUST match App\Support\Cartography
 *              viewBox 0..1000 × 0..800 covers lng 38.5..48.8, lat 29.0..37.5
 *
 * Usage:
 *   cd delos-website && node scripts/generate-iraq-svg.mjs
 *
 * Safe to re-run any time — idempotent, fetches fresh data each call.
 */

import fs from 'node:fs/promises';
import path from 'node:path';
import { feature } from 'topojson-client';

// ─── Projection constants (MUST match App\Support\Cartography) ─────
const LNG_MIN = 38.5;
const LNG_MAX = 48.8;
const LAT_MIN = 29.0;
const LAT_MAX = 37.5;
const VB_W = 1000;
const VB_H = 800;

// Padding so neighbors aren't cropped right at the canvas edge.
const CLIP_PAD = 40;

function project([lng, lat]) {
    const x = ((lng - LNG_MIN) / (LNG_MAX - LNG_MIN)) * VB_W;
    const y = VB_H - ((lat - LAT_MIN) / (LAT_MAX - LAT_MIN)) * VB_H;
    return [Math.round(x * 100) / 100, Math.round(y * 100) / 100];
}

/**
 * Douglas–Peucker polyline simplification. Tolerance is in SVG-unit space
 * (so ~0.15 ≈ ~140 m at Iraq's latitude — fine detail without noise).
 */
function simplify(points, tolerance) {
    if (points.length < 3) return points;
    const sqTol = tolerance * tolerance;

    function sqDist([x, y], [x1, y1], [x2, y2]) {
        let dx = x2 - x1;
        let dy = y2 - y1;
        if (dx !== 0 || dy !== 0) {
            const t = ((x - x1) * dx + (y - y1) * dy) / (dx * dx + dy * dy);
            if (t > 1) { x1 = x2; y1 = y2; }
            else if (t > 0) { x1 += dx * t; y1 += dy * t; }
        }
        dx = x - x1; dy = y - y1;
        return dx * dx + dy * dy;
    }

    function dpStep(points, first, last, sqTol, out) {
        let maxSqDist = sqTol;
        let index = -1;
        for (let i = first + 1; i < last; i++) {
            const d = sqDist(points[i], points[first], points[last]);
            if (d > maxSqDist) { index = i; maxSqDist = d; }
        }
        if (index > -1) {
            if (index - first > 1) dpStep(points, first, index, sqTol, out);
            out.push(points[index]);
            if (last - index > 1) dpStep(points, index, last, sqTol, out);
        }
    }

    const out = [points[0]];
    dpStep(points, 0, points.length - 1, sqTol, out);
    out.push(points[points.length - 1]);
    return out;
}

function dedup(points) {
    if (points.length === 0) return points;
    const out = [points[0]];
    for (let i = 1; i < points.length; i++) {
        const [px, py] = points[i - 1];
        const [x, y] = points[i];
        if (Math.abs(x - px) > 0.01 || Math.abs(y - py) > 0.01) {
            out.push([x, y]);
        }
    }
    return out;
}

function ringToPath(ring, tolerance, closed = true) {
    const projected = ring.map(project);
    const d1 = dedup(projected);
    const simplified = simplify(d1, tolerance);
    if (simplified.length < (closed ? 3 : 2)) return '';
    const [mx, my] = simplified[0];
    let d = `M ${mx} ${my}`;
    for (let i = 1; i < simplified.length; i++) {
        d += ` L ${simplified[i][0]} ${simplified[i][1]}`;
    }
    if (closed) d += ' Z';
    return d;
}

function polygonsToPath(geom, tolerance) {
    const polygons = geom.type === 'Polygon' ? [geom.coordinates] : geom.coordinates;
    const segments = [];
    for (const polygon of polygons) {
        for (const ring of polygon) {
            const d = ringToPath(ring, tolerance, true);
            if (d) segments.push(d);
        }
    }
    return segments.join(' ');
}

/**
 * Render a polygon's outline as an open polyline (no fill), clipped to the
 * padded viewBox. Used for neighbors: we only need the parts of their border
 * that are visible on our map, not the whole country.
 */
function polygonBorderClipped(geom, tolerance) {
    const polygons = geom.type === 'Polygon' ? [geom.coordinates] : geom.coordinates;
    const parts = [];
    for (const polygon of polygons) {
        for (const ring of polygon) {
            // Treat the ring as a closed line (first = last), project, dedup,
            // then clip to padded viewBox so only the visible border remains.
            const projected = ring.map(project);
            const dedped = dedup(projected);
            const clipped = clipLineToBounds(dedped);
            for (const segment of clipped) {
                const simplified = simplify(segment, tolerance);
                if (simplified.length < 2) continue;
                const [mx, my] = simplified[0];
                let d = `M ${mx} ${my}`;
                for (let i = 1; i < simplified.length; i++) {
                    d += ` L ${simplified[i][0]} ${simplified[i][1]}`;
                }
                parts.push(d);
            }
        }
    }
    return parts.join(' ');
}

// ─── Clip a line segment against the padded viewBox ──────────────
//   Keeps only the parts of a LineString that fall inside the canvas
//   (Liang-Barsky). Used for rivers that exit Iraq's bounds.
function clipLineToBounds(points) {
    const xMin = -CLIP_PAD, xMax = VB_W + CLIP_PAD;
    const yMin = -CLIP_PAD, yMax = VB_H + CLIP_PAD;
    const segments = [];
    let current = [];

    function inside([x, y]) {
        return x >= xMin && x <= xMax && y >= yMin && y <= yMax;
    }

    function clipEdge(p1, p2) {
        // Liang-Barsky
        let [x1, y1] = p1;
        let [x2, y2] = p2;
        let t0 = 0, t1 = 1;
        const dx = x2 - x1;
        const dy = y2 - y1;
        const clip = (p, q) => {
            if (p === 0) { if (q < 0) return false; return true; }
            const t = q / p;
            if (p < 0) { if (t > t1) return false; if (t > t0) t0 = t; }
            else { if (t < t0) return false; if (t < t1) t1 = t; }
            return true;
        };
        if (clip(-dx, x1 - xMin) && clip(dx, xMax - x1) &&
            clip(-dy, y1 - yMin) && clip(dy, yMax - y1)) {
            const cx1 = x1 + t0 * dx;
            const cy1 = y1 + t0 * dy;
            const cx2 = x1 + t1 * dx;
            const cy2 = y1 + t1 * dy;
            return [[cx1, cy1], [cx2, cy2]];
        }
        return null;
    }

    for (let i = 0; i < points.length - 1; i++) {
        const a = points[i];
        const b = points[i + 1];
        const aIn = inside(a);
        const bIn = inside(b);

        if (aIn && bIn) {
            if (current.length === 0) current.push(a);
            current.push(b);
        } else {
            const clipped = clipEdge(a, b);
            if (!clipped) {
                if (current.length) { segments.push(current); current = []; }
                continue;
            }
            const [ca, cb] = clipped;
            if (aIn && !bIn) {
                if (current.length === 0) current.push(a);
                current.push(cb);
                segments.push(current);
                current = [];
            } else if (!aIn && bIn) {
                if (current.length) { segments.push(current); current = []; }
                current.push(ca, b);
            } else {
                // both outside but segment crosses canvas
                segments.push([ca, cb]);
            }
        }
    }
    if (current.length) segments.push(current);
    return segments;
}

function lineStringsToPath(coords, tolerance) {
    // coords may be LineString or MultiLineString-compatible
    const lines = Array.isArray(coords[0][0]) && typeof coords[0][0][0] === 'number'
        ? coords          // MultiLineString
        : [coords];       // LineString

    const parts = [];
    for (const line of lines) {
        const projected = line.map(project);
        const d1 = dedup(projected);
        const clipped = clipLineToBounds(d1);
        for (const segment of clipped) {
            const simplified = simplify(segment, tolerance);
            if (simplified.length < 2) continue;
            const [mx, my] = simplified[0];
            let d = `M ${mx} ${my}`;
            for (let i = 1; i < simplified.length; i++) {
                d += ` L ${simplified[i][0]} ${simplified[i][1]}`;
            }
            parts.push(d);
        }
    }
    return parts.join(' ');
}

// ─── Fetch helpers ────────────────────────────────────────────────
async function fetchJson(url) {
    console.log(`→ Fetching ${url}`);
    const res = await fetch(url);
    if (!res.ok) {
        console.error(`  Failed: HTTP ${res.status}`);
        process.exit(1);
    }
    return res.json();
}

// ─── Main ─────────────────────────────────────────────────────────
const COUNTRIES_URL = 'https://unpkg.com/world-atlas@2/countries-10m.json';
const RIVERS_URL    = 'https://raw.githubusercontent.com/nvkelso/natural-earth-vector/master/geojson/ne_10m_rivers_lake_centerlines.geojson';

// ISO-3166 numeric codes
const IRAQ_ID = '368';
const NEIGHBOR_IDS = {
    '792': 'Turkey',
    '364': 'Iran',
    '760': 'Syria',
    '400': 'Jordan',
    '682': 'Saudi Arabia',
    '414': 'Kuwait',
};

const IRAQ_TOLERANCE = 0.15;
const NEIGHBOR_TOLERANCE = 0.35;
const RIVER_TOLERANCE = 0.15;

// 1. Countries
const topology = await fetchJson(COUNTRIES_URL);
console.log('→ Decoding countries TopoJSON → GeoJSON');
const countries = feature(topology, topology.objects.countries);

const iraq = countries.features.find(f => String(f.id) === IRAQ_ID);
if (!iraq) { console.error('  Could not find Iraq (id 368)'); process.exit(1); }
console.log(`  Iraq (${iraq.geometry.type})`);

const iraqPath = polygonsToPath(iraq.geometry, IRAQ_TOLERANCE);
const iraqVertexCount = (iraqPath.match(/[ML]/g) || []).length;
console.log(`  Iraq outline: ${iraqVertexCount} vertices at tolerance ${IRAQ_TOLERANCE}`);

const neighborSegments = [];
for (const [id, name] of Object.entries(NEIGHBOR_IDS)) {
    const f = countries.features.find(x => String(x.id) === id);
    if (!f) { console.warn(`  ! Skipped ${name} (${id}) — not found`); continue; }
    const d = polygonBorderClipped(f.geometry, NEIGHBOR_TOLERANCE);
    if (d) neighborSegments.push({ name, d });
    const v = (d.match(/[ML]/g) || []).length;
    console.log(`  Neighbor ${name} (${id}): ${v} vertices`);
}

// 2. Rivers — Tigris, Euphrates, Shatt al Arab
console.log('');
const rivers = await fetchJson(RIVERS_URL);
const WANTED_RIVERS = ['Tigris', 'Euphrates', 'Shatt al Arab'];
const riverSegments = [];
for (const name of WANTED_RIVERS) {
    const matches = rivers.features.filter(f => {
        const n = (f.properties.name || '') + '|' + (f.properties.name_en || '');
        return n.includes(name);
    });
    if (matches.length === 0) { console.warn(`  ! No river match for ${name}`); continue; }
    // A river may appear as multiple features (tributaries, main stem) — merge all
    const subPaths = matches
        .map(f => lineStringsToPath(f.geometry.coordinates, RIVER_TOLERANCE))
        .filter(Boolean);
    const d = subPaths.join(' ');
    if (d) riverSegments.push({ name, d });
    const v = (d.match(/[ML]/g) || []).length;
    console.log(`  River ${name}: ${matches.length} feature(s) → ${v} vertices (clipped to bounds)`);
}

// ─── Compose SVG ──────────────────────────────────────────────────
const neighborPathEls = neighborSegments
    .map(({ name, d }) => `        <path class="iraq-map__neighbor" data-country="${name}" d="${d}" vector-effect="non-scaling-stroke" />`)
    .join('\n');

const riverPathEls = riverSegments
    .map(({ name, d }) => `        <path class="iraq-map__river" data-river="${name}" d="${d}" vector-effect="non-scaling-stroke" />`)
    .join('\n');

const svg = `<svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 ${VB_W} ${VB_H}"
    preserveAspectRatio="xMidYMid meet"
    fill="none"
    role="img"
    aria-labelledby="iraq-map-title"
>
    <title id="iraq-map-title">Outline of Iraq showing Delos showroom locations</title>

    <!--
        Generated from Natural Earth 1:10m (public domain) via
        scripts/generate-iraq-svg.mjs. Do not edit this file by hand — re-run
        the script to regenerate.

        Geography:
          • Iraq outline       — 1:10m countries, simplified to ${IRAQ_TOLERANCE} SVG units
          • Neighbor outlines  — Turkey, Iran, Syria, Jordan, Saudi Arabia, Kuwait
                                 (hair-line, drawn behind Iraq for context)
          • Rivers             — real Tigris, Euphrates, Shatt al Arab centerlines

        Projection matches App\\Support\\Cartography:
          x = ((lng - ${LNG_MIN}) / ${(LNG_MAX - LNG_MIN).toFixed(1)}) * ${VB_W}
          y = ${VB_H} - ((lat - ${LAT_MIN}) / ${(LAT_MAX - LAT_MIN).toFixed(1)}) * ${VB_H}
    -->
    <g class="iraq-map__geography">
        <!-- Neighboring countries — faint context layer, drawn first so Iraq overlays cleanly -->
${neighborPathEls}

        <!-- Iraq country outline -->
        <path
            class="iraq-map__outline"
            d="${iraqPath}"
            vector-effect="non-scaling-stroke"
        />

        <!-- Tigris + Euphrates + Shatt al Arab, real centerlines -->
${riverPathEls}
    </g>
</svg>
`;

const outPath = path.resolve(path.dirname(new URL(import.meta.url).pathname), '..', 'resources', 'svg', 'iraq-map.svg');
await fs.writeFile(outPath, svg, 'utf8');
const stat = await fs.stat(outPath);
console.log('');
console.log(`✓ Wrote ${outPath} (${(stat.size / 1024).toFixed(2)} KB)`);
