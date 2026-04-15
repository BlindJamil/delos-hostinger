#!/usr/bin/env node
/**
 * Accuracy verification for resources/svg/iraq-map.svg.
 *
 * Runs two tests:
 *   1. Point-in-polygon: for a set of known cities, confirm that cities
 *      INSIDE Iraq fall inside the rendered outline, and cities OUTSIDE
 *      Iraq (e.g. Tehran, Damascus, Riyadh) fall outside.
 *   2. Distance check: for each Iraq border city, measure the projected
 *      pin position vs the nearest outline vertex, in SVG units. A good
 *      match means pins sit exactly on the border.
 *
 * Usage: node scripts/verify-iraq-accuracy.mjs
 */

import fs from 'node:fs/promises';

// Must match App\Support\Cartography + generate-iraq-svg.mjs
const LNG_MIN = 38.5, LNG_MAX = 48.8, LAT_MIN = 29.0, LAT_MAX = 37.5;
const VB_W = 1000, VB_H = 800;
function project([lng, lat]) {
    return [
        ((lng - LNG_MIN) / (LNG_MAX - LNG_MIN)) * VB_W,
        VB_H - ((lat - LAT_MIN) / (LAT_MAX - LAT_MIN)) * VB_H,
    ];
}

// Parse the outline path out of the SVG
const svg = await fs.readFile('resources/svg/iraq-map.svg', 'utf8');
const match = svg.match(/class="iraq-map__outline"[\s\S]*?d="([^"]+)"/);
if (!match) { console.error('Outline path not found'); process.exit(1); }
const d = match[1];

// Extract all M and L points into a polygon
const points = [];
const re = /[ML]\s*([\d.-]+)\s+([\d.-]+)/g;
let m;
while ((m = re.exec(d)) !== null) {
    points.push([parseFloat(m[1]), parseFloat(m[2])]);
}
console.log(`Outline: ${points.length} vertices\n`);

// Ray-casting point-in-polygon
function inside([x, y], poly) {
    let c = false;
    for (let i = 0, j = poly.length - 1; i < poly.length; j = i++) {
        const [xi, yi] = poly[i], [xj, yj] = poly[j];
        const intersect = ((yi > y) !== (yj > y)) &&
            (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
        if (intersect) c = !c;
    }
    return c;
}

// ─── Test 1: cities inside vs outside Iraq ─────────────────────
console.log('━━━ Test 1: point-in-polygon ━━━\n');

const tests = [
    // Should be INSIDE Iraq:
    { name: 'Baghdad',       lat: 33.3152, lng: 44.3661, expected: 'in' },
    { name: 'Erbil',         lat: 36.1911, lng: 44.0094, expected: 'in' },
    { name: 'Basra',         lat: 30.5085, lng: 47.7804, expected: 'in' },
    { name: 'Mosul',         lat: 36.3395, lng: 43.1253, expected: 'in' },
    { name: 'Kirkuk',        lat: 35.4681, lng: 44.3922, expected: 'in' },
    { name: 'Sulaymaniyah',  lat: 35.5617, lng: 45.4309, expected: 'in' },
    { name: 'Najaf',         lat: 32.0000, lng: 44.3333, expected: 'in' },
    { name: 'Ramadi',        lat: 33.4206, lng: 43.3072, expected: 'in' },
    { name: 'Faw (SE tip)',  lat: 30.0000, lng: 48.4750, expected: 'in' },
    // Should be OUTSIDE Iraq:
    { name: 'Tehran (IR)',   lat: 35.6892, lng: 51.3890, expected: 'out' },
    { name: 'Damascus (SY)', lat: 33.5138, lng: 36.2765, expected: 'out' },
    { name: 'Riyadh (SA)',   lat: 24.7136, lng: 46.6753, expected: 'out' }, // south of viewBox
    { name: 'Ankara (TR)',   lat: 39.9334, lng: 32.8597, expected: 'out' }, // north of viewBox
    { name: 'Amman (JO)',    lat: 31.9454, lng: 35.9284, expected: 'out' },
    { name: 'Kuwait City',   lat: 29.3759, lng: 47.9774, expected: 'out' },
];

let pass = 0, fail = 0;
for (const t of tests) {
    const [x, y] = project([t.lng, t.lat]);
    // If the city's (lng,lat) is outside the projection viewBox, it's definitionally outside the country too.
    const withinViewBox = x >= 0 && x <= VB_W && y >= 0 && y <= VB_H;
    const inPoly = withinViewBox && inside([x, y], points);
    const actual = inPoly ? 'in' : 'out';
    const ok = actual === t.expected;
    ok ? pass++ : fail++;
    console.log(`  ${ok ? '✓' : '✗'} ${t.name.padEnd(16)} (${t.lng.toFixed(3)}, ${t.lat.toFixed(3)}) → projected (${x.toFixed(0)}, ${y.toFixed(0)}) → expected ${t.expected}, got ${actual}`);
}
console.log(`\n  ${pass}/${pass + fail} passed\n`);

// ─── Test 2: distance-to-nearest-vertex for border landmarks ───
console.log('━━━ Test 2: border landmark distance ━━━\n');

const borderLandmarks = [
    { name: 'Zakho (NW corner)',    lat: 37.1429, lng: 42.6870 },
    { name: 'Sinjar (NW)',          lat: 36.3189, lng: 41.8379 },
    { name: 'Al-Qaim (W border)',   lat: 34.3885, lng: 41.0065 },
    { name: 'Faw peninsula (SE)',   lat: 30.0000, lng: 48.4750 },
    { name: 'Umm Qasr (S coast)',   lat: 30.0333, lng: 47.9333 },
];

function dist([x1, y1], [x2, y2]) {
    return Math.hypot(x1 - x2, y1 - y2);
}

for (const c of borderLandmarks) {
    const p = project([c.lng, c.lat]);
    let minD = Infinity;
    for (const v of points) {
        const d = dist(p, v);
        if (d < minD) minD = d;
    }
    // Convert SVG units back to real km: 10.3° longitude ≈ 930km at Iraq's latitude
    const kmPerUnit = 930 / VB_W;
    const km = minD * kmPerUnit;
    console.log(`  ${c.name.padEnd(26)} → nearest outline vertex is ${minD.toFixed(1)} SVG units (≈ ${km.toFixed(1)} km)`);
}
console.log('');

console.log(`Dataset: Natural Earth 1:50m via unpkg.com/world-atlas@2`);
console.log(`         (public domain, used by NYT/BBC/Economist for editorial maps)`);
console.log(`Pipeline: TopoJSON → feature id=368 (Iraq) → ${points.length} vertices after Douglas-Peucker (tolerance 0.6)\n`);
