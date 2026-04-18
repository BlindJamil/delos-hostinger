#!/usr/bin/env node
/**
 * One-shot build script — regenerates resources/svg/iraq-map.svg from
 * authoritative cartographic sources. Editorial-grade output for the DELOS
 * branches page:
 *
 *   • Iraq outline       — HDX Common Operational Dataset for Iraq (UN OCHA),
 *                          tolerance 0.05 SVG units (~46 m) so Al-Faw peninsula
 *                          and desert surveyed straights render at full fidelity.
 *   • Governorates       — HDX admin1 (18 governorates; Halabja not yet in
 *                          public open-data releases — will appear automatically
 *                          once HDX publishes the 19-governorate update).
 *   • Kurdistan Region   — subtle gold tint on Duhok + Erbil + Sulaymaniyah.
 *   • Neighbors          — Natural Earth 1:10m (Turkey, Iran, Syria, Jordan,
 *                          Saudi Arabia, Kuwait), clipped to viewBox.
 *   • Rivers             — Natural Earth 1:10m Tigris, Euphrates, Shatt al Arab.
 *
 * Sources (all public domain or open-licence):
 *   HDX IRQ CODAB : https://data.humdata.org/dataset/cod-ab-irq
 *   Countries 10m : https://unpkg.com/world-atlas@2/countries-10m.json
 *   Rivers 10m    : https://raw.githubusercontent.com/nvkelso/natural-earth-vector/master/geojson/ne_10m_rivers_lake_centerlines.geojson
 *
 * Projection : linear lat/lng → SVG mapping, MUST match App\Support\Cartography
 *              viewBox 0..1000 × 0..800 covers lng 38.5..48.8, lat 29.0..37.5
 *
 * Usage:
 *   cd delos-website && node scripts/generate-iraq-svg.mjs
 *
 * Dependencies: node ≥ 18 (fetch, top-level await) and the `unzip` CLI
 * (installed by default on macOS / Linux / Hostinger).
 *
 * Safe to re-run any time — idempotent, fetches fresh data each call.
 */

import fs from 'node:fs/promises';
import path from 'node:path';
import os from 'node:os';
import { execFileSync } from 'node:child_process';
import { feature } from 'topojson-client';

// ─── Projection constants (MUST match App\Support\Cartography) ─────
const LNG_MIN = 38.5;
const LNG_MAX = 48.8;
const LAT_MIN = 29.0;
const LAT_MAX = 37.5;
const VB_W = 1000;
const VB_H = 800;
const CLIP_PAD = 40;

const IRAQ_TOLERANCE        = 0.05;  // ~46 m — preserve Faw + surveyed borders
const GOVERNORATE_TOLERANCE = 0.25;  // ~230 m — crisp but unobtrusive interior
const NEIGHBOR_TOLERANCE    = 0.35;
const RIVER_TOLERANCE       = 0.15;

// Governorates that make up the Kurdistan Region (HDX pcode → name)
const KURDISTAN_PCODES = new Set(['IQG09', 'IQG11', 'IQG06']); // Duhok, Erbil, Sulaymaniyah

// Data sources
const HDX_ZIP_URL       = 'https://data.humdata.org/dataset/488bb3cd-3ce9-49d3-862a-3ce7975c63e1/resource/a9ed4d00-1182-4fdc-a0e4-82bbd20786db/download/irq_admin_boundaries.geojson.zip';
const NE_COUNTRIES_URL  = 'https://unpkg.com/world-atlas@2/countries-10m.json';
const NE_RIVERS_URL     = 'https://raw.githubusercontent.com/nvkelso/natural-earth-vector/master/geojson/ne_10m_rivers_lake_centerlines.geojson';

const IRAQ_ID = '368';
const NEIGHBOR_IDS = {
    '792': 'Turkey',
    '364': 'Iran',
    '760': 'Syria',
    '400': 'Jordan',
    '682': 'Saudi Arabia',
    '414': 'Kuwait',
};

const MAX_OUTPUT_BYTES = 200 * 1024; // abort if output exceeds 200 KB

// ─── Geometry helpers ─────────────────────────────────────────────
function project([lng, lat]) {
    const x = ((lng - LNG_MIN) / (LNG_MAX - LNG_MIN)) * VB_W;
    const y = VB_H - ((lat - LAT_MIN) / (LAT_MAX - LAT_MIN)) * VB_H;
    return [Math.round(x * 100) / 100, Math.round(y * 100) / 100];
}

/**
 * Douglas–Peucker polyline simplification. Tolerance is in SVG-unit space.
 */
function simplify(points, tolerance) {
    if (points.length < 3 || tolerance <= 0) return points;
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

// Liang-Barsky clipping of a line segment to the padded viewBox
function clipLineToBounds(points) {
    const xMin = -CLIP_PAD, xMax = VB_W + CLIP_PAD;
    const yMin = -CLIP_PAD, yMax = VB_H + CLIP_PAD;
    const segments = [];
    let current = [];

    function inside([x, y]) {
        return x >= xMin && x <= xMax && y >= yMin && y <= yMax;
    }

    function clipEdge(p1, p2) {
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
                segments.push([ca, cb]);
            }
        }
    }
    if (current.length) segments.push(current);
    return segments;
}

/**
 * Render a polygon as clipped open border lines (used for neighbours and
 * governorate interior boundaries — we only want the visible border segments).
 */
function polygonBorderClipped(geom, tolerance) {
    const polygons = geom.type === 'Polygon' ? [geom.coordinates] : geom.coordinates;
    const parts = [];
    for (const polygon of polygons) {
        for (const ring of polygon) {
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

function lineStringsToPath(coords, tolerance) {
    const lines = Array.isArray(coords[0][0]) && typeof coords[0][0][0] === 'number'
        ? coords
        : [coords];

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
    if (!res.ok) throw new Error(`HTTP ${res.status} from ${url}`);
    return res.json();
}

async function fetchZip(url) {
    console.log(`→ Fetching ${url}`);
    const res = await fetch(url);
    if (!res.ok) throw new Error(`HTTP ${res.status} from ${url}`);
    const buf = Buffer.from(await res.arrayBuffer());
    return buf;
}

async function extractHdxGeoJson() {
    // Fetch zip → write to temp → unzip just the files we need.
    const tmpDir = await fs.mkdtemp(path.join(os.tmpdir(), 'delos-hdx-'));
    const zipPath = path.join(tmpDir, 'hdx.zip');
    const buf = await fetchZip(HDX_ZIP_URL);
    await fs.writeFile(zipPath, buf);

    // Require `unzip` on PATH. Fail clean if missing.
    try {
        execFileSync('unzip', ['-q', '-o', zipPath, '-d', tmpDir], { stdio: 'pipe' });
    } catch (err) {
        throw new Error(
            `Failed to run \`unzip\` (required to extract HDX archive). ` +
            `Install on Debian/Ubuntu: \`sudo apt-get install unzip\`. Error: ${err.message}`
        );
    }

    const admin0 = JSON.parse(await fs.readFile(path.join(tmpDir, 'irq_admin0.geojson'), 'utf8'));
    const admin1 = JSON.parse(await fs.readFile(path.join(tmpDir, 'irq_admin1.geojson'), 'utf8'));

    // Clean up temp dir (best effort)
    fs.rm(tmpDir, { recursive: true, force: true }).catch(() => {});

    return { admin0, admin1 };
}

// ─── Main ─────────────────────────────────────────────────────────
async function main() {
    // 1. Iraq outline + governorates from HDX
    let iraqPath, governorateSegments, regionFills, iraqVertexCount, govVertexCount;
    let geographySource = 'HDX Common Operational Dataset for Iraq (UN OCHA)';
    try {
        const { admin0, admin1 } = await extractHdxGeoJson();
        console.log(`  HDX admin0: 1 feature (${admin0.features[0].geometry.type})`);
        console.log(`  HDX admin1: ${admin1.features.length} governorates`);

        iraqPath = polygonsToPath(admin0.features[0].geometry, IRAQ_TOLERANCE);
        iraqVertexCount = (iraqPath.match(/[ML]/g) || []).length;
        console.log(`  Iraq outline: ${iraqVertexCount} vertices at tolerance ${IRAQ_TOLERANCE}`);

        // Kurdistan region fills — drawn BEHIND Iraq outline
        regionFills = admin1.features
            .filter(f => KURDISTAN_PCODES.has(f.properties.adm1_pcode))
            .map(f => ({
                name: f.properties.adm1_name,
                pcode: f.properties.adm1_pcode,
                d: polygonsToPath(f.geometry, GOVERNORATE_TOLERANCE),
            }))
            .filter(r => r.d);

        // Governorate interior boundaries (open polylines, clipped)
        governorateSegments = admin1.features.map(f => ({
            name: f.properties.adm1_name,
            pcode: f.properties.adm1_pcode,
            d: polygonBorderClipped(f.geometry, GOVERNORATE_TOLERANCE),
        })).filter(g => g.d);

        govVertexCount = governorateSegments.reduce(
            (s, g) => s + (g.d.match(/[ML]/g) || []).length, 0);
        console.log(`  Governorate borders: ${govVertexCount} vertices across ${governorateSegments.length} governorates`);
        console.log(`  Kurdistan Region fills: ${regionFills.length} governorates`);
    } catch (err) {
        console.warn(`  ! HDX fetch failed (${err.message}) — falling back to Natural Earth 10m`);
        geographySource = 'Natural Earth 1:10m (HDX fallback)';
        const topology = await fetchJson(NE_COUNTRIES_URL);
        const countries = feature(topology, topology.objects.countries);
        const iraqFeature = countries.features.find(f => String(f.id) === IRAQ_ID);
        if (!iraqFeature) throw new Error('Fallback failed: Iraq not in Natural Earth dataset');
        iraqPath = polygonsToPath(iraqFeature.geometry, 0.15);
        iraqVertexCount = (iraqPath.match(/[ML]/g) || []).length;
        governorateSegments = [];
        regionFills = [];
        govVertexCount = 0;
    }

    // 2. Neighbour countries from Natural Earth 10m
    const topology = await fetchJson(NE_COUNTRIES_URL);
    const countries = feature(topology, topology.objects.countries);
    const neighborSegments = [];
    for (const [id, name] of Object.entries(NEIGHBOR_IDS)) {
        const f = countries.features.find(x => String(x.id) === id);
        if (!f) { console.warn(`  ! Skipped ${name} (${id})`); continue; }
        const d = polygonBorderClipped(f.geometry, NEIGHBOR_TOLERANCE);
        if (d) neighborSegments.push({ name, d });
        console.log(`  Neighbor ${name} (${id}): ${(d.match(/[ML]/g) || []).length} vertices`);
    }

    // 3. Rivers from Natural Earth 10m
    console.log('');
    const rivers = await fetchJson(NE_RIVERS_URL);
    const WANTED_RIVERS = ['Tigris', 'Euphrates', 'Shatt al Arab'];
    const riverSegments = [];
    for (const name of WANTED_RIVERS) {
        const matches = rivers.features.filter(f => {
            const n = (f.properties.name || '') + '|' + (f.properties.name_en || '');
            return n.includes(name);
        });
        if (matches.length === 0) { console.warn(`  ! No river match for ${name}`); continue; }
        const subPaths = matches
            .map(f => lineStringsToPath(f.geometry.coordinates, RIVER_TOLERANCE))
            .filter(Boolean);
        const d = subPaths.join(' ');
        if (d) riverSegments.push({ name, d });
        console.log(`  River ${name}: ${(d.match(/[ML]/g) || []).length} vertices`);
    }

    // ─── Compose SVG ──────────────────────────────────────────────
    const regionFillEls = regionFills
        .map(({ name, pcode, d }) => `        <path class="iraq-map__region iraq-map__region--kurdistan" data-gov="${name}" data-pcode="${pcode}" d="${d}" />`)
        .join('\n');

    const governorateEls = governorateSegments
        .map(({ name, pcode, d }) => `        <path class="iraq-map__governorate" data-gov="${name}" data-pcode="${pcode}" d="${d}" vector-effect="non-scaling-stroke" />`)
        .join('\n');

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
        Generated by scripts/generate-iraq-svg.mjs — do not edit by hand.

        Geography:
          • Iraq outline       — ${geographySource}, simplified to ${IRAQ_TOLERANCE} SVG units
          • Governorate borders — HDX admin1 (18 governorates; Halabja not yet
                                  in public releases — will appear automatically
                                  once HDX publishes the 19-governorate update)
          • Kurdistan Region   — subtle gold tint on Duhok + Erbil + Sulaymaniyah
          • Neighbour outlines — Natural Earth 1:10m, hair-lines behind Iraq
          • Rivers             — real Tigris, Euphrates, Shatt al Arab centerlines

        Projection matches App\\Support\\Cartography:
          x = ((lng - ${LNG_MIN}) / ${(LNG_MAX - LNG_MIN).toFixed(1)}) * ${VB_W}
          y = ${VB_H} - ((lat - ${LAT_MIN}) / ${(LAT_MAX - LAT_MIN).toFixed(1)}) * ${VB_H}
    -->
    <g class="iraq-map__geography">
        <!-- Kurdistan Region fills (drawn first, behind everything else) -->
${regionFillEls || '        <!-- (no region fills) -->'}

        <!-- Neighbouring countries — faint context layer -->
${neighborPathEls}

        <!-- Governorate interior borders — hair-line -->
${governorateEls || '        <!-- (no governorate borders) -->'}

        <!-- Iraq country outline — drawn above governorate borders so the
             international boundary reads as the strongest line on the map. -->
        <path
            class="iraq-map__outline"
            d="${iraqPath}"
            vector-effect="non-scaling-stroke"
        />

        <!-- Tigris + Euphrates + Shatt al Arab centerlines -->
${riverPathEls}
    </g>
</svg>
`;

    const outPath = path.resolve(
        path.dirname(new URL(import.meta.url).pathname),
        '..', 'resources', 'svg', 'iraq-map.svg'
    );
    await fs.writeFile(outPath, svg, 'utf8');
    const stat = await fs.stat(outPath);
    console.log('');
    console.log(`✓ Wrote ${outPath} (${(stat.size / 1024).toFixed(2)} KB)`);

    if (stat.size > MAX_OUTPUT_BYTES) {
        console.error(`✗ Output exceeds ${MAX_OUTPUT_BYTES} bytes — check simplification tolerances`);
        process.exit(1);
    }
}

await main();
