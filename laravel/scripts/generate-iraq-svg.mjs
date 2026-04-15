#!/usr/bin/env node
/**
 * One-shot build script — regenerates resources/svg/iraq-map.svg from the
 * world-atlas Natural Earth dataset (public domain, Mike Bostock / D3).
 *
 * Input : https://unpkg.com/world-atlas@2/countries-50m.json (TopoJSON)
 * Extract: feature id === "368" (ISO-3166 numeric for Iraq)
 * Project: same linear lat/lng → SVG mapping as App\Support\Cartography
 *          (viewBox 0..1000 × 0..800 covers lng 38.5..48.8, lat 29.0..37.5)
 * Output : resources/svg/iraq-map.svg with one accurate outline path + the
 *          existing decorative river schematics (hand-drawn, preserved).
 *
 * Usage:
 *   cd delos-website && node scripts/generate-iraq-svg.mjs
 *
 * Safe to re-run any time — idempotent, fetches fresh TopoJSON each call.
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

function project([lng, lat]) {
    const x = ((lng - LNG_MIN) / (LNG_MAX - LNG_MIN)) * VB_W;
    const y = VB_H - ((lat - LAT_MIN) / (LAT_MAX - LAT_MIN)) * VB_H;
    return [Math.round(x * 100) / 100, Math.round(y * 100) / 100];
}

/**
 * Douglas–Peucker polyline simplification. Keeps visually significant
 * vertices, drops ones that barely shift the line. Tolerance is in the
 * projected SVG coordinate space (so ~0.5 unit ≈ ~0.05% of the viewBox
 * width — a pixel at typical screen sizes).
 */
function simplify(points, tolerance = 0.5) {
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

function ringToPath(ring) {
    // Ring is GeoJSON [lng, lat] pairs, first === last (closed).
    // Project, de-duplicate consecutive identical pixels, simplify, emit M…L…Z.
    const projected = ring.map(project);
    const dedup = [projected[0]];
    for (let i = 1; i < projected.length; i++) {
        const [px, py] = projected[i - 1];
        const [x, y] = projected[i];
        if (Math.abs(x - px) > 0.01 || Math.abs(y - py) > 0.01) {
            dedup.push([x, y]);
        }
    }
    const simplified = simplify(dedup, 0.6);
    if (simplified.length < 3) return '';
    const [mx, my] = simplified[0];
    let d = `M ${mx} ${my}`;
    for (let i = 1; i < simplified.length; i++) {
        d += ` L ${simplified[i][0]} ${simplified[i][1]}`;
    }
    d += ' Z';
    return d;
}

// ─── Main ─────────────────────────────────────────────────────────

const SOURCE_URL = 'https://unpkg.com/world-atlas@2/countries-50m.json';
console.log(`→ Fetching ${SOURCE_URL}`);
const response = await fetch(SOURCE_URL);
if (!response.ok) {
    console.error(`  Failed: HTTP ${response.status}`);
    process.exit(1);
}
const topology = await response.json();

console.log('→ Decoding TopoJSON → GeoJSON');
const countries = feature(topology, topology.objects.countries);
const iraq = countries.features.find(f => String(f.id) === '368');
if (!iraq) {
    console.error('  Could not find Iraq (feature id "368") in dataset');
    process.exit(1);
}

console.log(`  Found Iraq (${iraq.geometry.type})`);

const polygons = iraq.geometry.type === 'Polygon'
    ? [iraq.geometry.coordinates]
    : iraq.geometry.coordinates; // MultiPolygon

let totalPoints = 0;
const pathSegments = [];
for (const polygon of polygons) {
    for (const ring of polygon) {
        totalPoints += ring.length;
        const d = ringToPath(ring);
        if (d) pathSegments.push(d);
    }
}
console.log(`  ${totalPoints} raw points → ${pathSegments.length} ring(s) after simplification`);

const outlinePath = pathSegments.join(' ');
const approxFinalPoints = (outlinePath.match(/[ML]/g) || []).length;
console.log(`  Final outline: ~${approxFinalPoints} vertices`);

// ─── Compose the full SVG — preserve rivers + viewBox + classes ────

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
        Iraq country outline, derived from the world-atlas Natural Earth 50m
        dataset (public domain, via Mike Bostock / D3). Simplified with
        Douglas-Peucker (tolerance 0.6 SVG units) — preserves the real shape
        (Turkey zigzag, Zagros jags on the Iran border, Shatt al-Arab tongue
        to the Persian Gulf, Saudi southern arc) while keeping file size
        under 15KB.

        Regenerate any time with:
            cd delos-website && node scripts/generate-iraq-svg.mjs

        Coordinate system matches App\\Support\\Cartography:
            x = ((lng - ${LNG_MIN}) / ${(LNG_MAX - LNG_MIN).toFixed(1)}) * ${VB_W}
            y = ${VB_H} - ((lat - ${LAT_MIN}) / ${(LAT_MAX - LAT_MIN).toFixed(1)}) * ${VB_H}
    -->
    <g class="iraq-map__geography">
        <path
            class="iraq-map__outline"
            d="${outlinePath}"
            vector-effect="non-scaling-stroke"
        />

        <!--
            Tigris + Euphrates schematic — hand-drawn Bezier curves, decorative
            only (not cartographically precise). Adds depth to the country
            outline so it doesn't read as a silhouette.
        -->
        <path
            class="iraq-map__river"
            d="M 500 6.6
               Q 475 180 580 330
               Q 650 480 830 660"
            vector-effect="non-scaling-stroke"
        />
        <path
            class="iraq-map__river"
            d="M 271.8 64
               Q 330 240 480 420
               Q 600 560 830 660"
            vector-effect="non-scaling-stroke"
        />
    </g>
</svg>
`;

const outPath = path.resolve(path.dirname(new URL(import.meta.url).pathname), '..', 'resources', 'svg', 'iraq-map.svg');
await fs.writeFile(outPath, svg, 'utf8');
const stat = await fs.stat(outPath);
console.log(`✓ Wrote ${outPath} (${(stat.size / 1024).toFixed(2)} KB)`);
