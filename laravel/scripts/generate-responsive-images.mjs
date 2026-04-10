import { readdirSync, statSync, mkdirSync, existsSync } from 'node:fs';
import path from 'node:path';
import process from 'node:process';
import sharp from 'sharp';

const projectRoot = process.cwd();
const sourceDir = path.join(projectRoot, 'public', 'images');
const outputDir = path.join(sourceDir, 'responsive');

const WIDTHS = [480, 768, 1200, 1600, 2000];
const WEBP_QUALITY = 78;
const JPEG_QUALITY = 82;
const SUPPORTED_EXT = new Set(['.jpg', '.jpeg', '.png', '.webp']);

mkdirSync(outputDir, { recursive: true });

function collectSourceImages(dir) {
    const entries = readdirSync(dir, { withFileTypes: true });
    const results = [];
    for (const entry of entries) {
        if (entry.isDirectory()) continue; // skip responsive/ subfolder and any others
        const ext = path.extname(entry.name).toLowerCase();
        if (!SUPPORTED_EXT.has(ext)) continue;
        results.push(path.join(dir, entry.name));
    }
    return results;
}

function needsRegeneration(sourcePath, variantPaths) {
    const sourceMtime = statSync(sourcePath).mtimeMs;
    for (const variant of variantPaths) {
        if (!existsSync(variant)) return true;
        if (statSync(variant).mtimeMs < sourceMtime) return true;
    }
    return false;
}

async function processImage(sourcePath) {
    const basename = path.basename(sourcePath, path.extname(sourcePath));
    const image = sharp(sourcePath);
    const metadata = await image.metadata();
    const sourceWidth = metadata.width ?? 0;

    // Only generate widths that are <= source width (don't upscale)
    const targetWidths = WIDTHS.filter((w) => w <= sourceWidth);
    if (targetWidths.length === 0) {
        targetWidths.push(sourceWidth);
    }

    const variantPaths = [];
    for (const width of targetWidths) {
        variantPaths.push(path.join(outputDir, `${basename}-${width}.webp`));
        variantPaths.push(path.join(outputDir, `${basename}-${width}.jpg`));
    }

    if (!needsRegeneration(sourcePath, variantPaths)) {
        return { processed: false, width: sourceWidth };
    }

    const sourceSize = statSync(sourcePath).size;
    let totalOutput = 0;

    for (const width of targetWidths) {
        const resized = sharp(sourcePath).resize({
            width,
            withoutEnlargement: true,
            kernel: sharp.kernel.lanczos3,
        });

        const webpPath = path.join(outputDir, `${basename}-${width}.webp`);
        await resized
            .clone()
            .webp({ quality: WEBP_QUALITY, effort: 5 })
            .toFile(webpPath);
        totalOutput += statSync(webpPath).size;

        const jpegPath = path.join(outputDir, `${basename}-${width}.jpg`);
        await resized
            .clone()
            .jpeg({ quality: JPEG_QUALITY, mozjpeg: true, progressive: true })
            .toFile(jpegPath);
        totalOutput += statSync(jpegPath).size;
    }

    return {
        processed: true,
        width: sourceWidth,
        sourceSize,
        outputSize: totalOutput,
        variants: targetWidths.length * 2,
    };
}

function formatBytes(bytes) {
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
    return `${(bytes / (1024 * 1024)).toFixed(2)} MB`;
}

async function run() {
    const sources = collectSourceImages(sourceDir);
    if (sources.length === 0) {
        console.log('No source images found in public/images/');
        return;
    }

    console.log(`Found ${sources.length} source image(s). Generating responsive variants...\n`);

    let processedCount = 0;
    let skippedCount = 0;
    let totalSourceSize = 0;
    let totalOutputSize = 0;
    let totalVariants = 0;

    for (const source of sources) {
        const name = path.relative(sourceDir, source);
        try {
            const result = await processImage(source);
            if (result.processed) {
                processedCount += 1;
                totalSourceSize += result.sourceSize;
                totalOutputSize += result.outputSize;
                totalVariants += result.variants;
                console.log(
                    `  ✓ ${name} (${result.width}px) → ${result.variants} variants, ${formatBytes(result.outputSize)}`,
                );
            } else {
                skippedCount += 1;
                console.log(`  - ${name} (up to date)`);
            }
        } catch (error) {
            console.error(`  ✗ ${name}: ${error.message}`);
        }
    }

    console.log('');
    console.log(`Processed: ${processedCount} image(s), ${totalVariants} variants`);
    console.log(`Skipped:   ${skippedCount} image(s) (unchanged)`);
    if (processedCount > 0) {
        console.log(`Sources:   ${formatBytes(totalSourceSize)}`);
        console.log(`Variants:  ${formatBytes(totalOutputSize)}`);
    }
}

run().catch((error) => {
    console.error('Image generation failed:', error);
    process.exit(1);
});
