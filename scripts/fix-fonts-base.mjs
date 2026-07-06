// laravel-vite-plugin's fonts feature hardcodes font @font-face URLs as
// `/${buildDirectory}/${fileName}` in public/build/fonts-manifest.json,
// ignoring Vite's resolved `base` entirely. Under a subdirectory deployment
// (VITE_BASE_PATH set to something other than the default /build/), this
// rewrites those baked-in paths to match.
import fs from 'node:fs';
import path from 'node:path';

const envPath = path.resolve('.env');
const envContent = fs.existsSync(envPath) ? fs.readFileSync(envPath, 'utf8') : '';
const match = envContent.match(/^VITE_BASE_PATH=(.*)$/m);
let base = (match ? match[1].trim() : '/build/').replace(/^["']|["']$/g, '');

if (!base || base === '/build/') {
    process.exit(0);
}

if (!base.endsWith('/')) {
    base += '/';
}

const manifestPath = path.resolve('public/build/fonts-manifest.json');
if (!fs.existsSync(manifestPath)) {
    process.exit(0);
}

const rewrite = (str) => str.split('/build/').join(base);

const manifest = JSON.parse(fs.readFileSync(manifestPath, 'utf8'));

if (manifest.style?.familyStyles) {
    for (const key of Object.keys(manifest.style.familyStyles)) {
        manifest.style.familyStyles[key] = rewrite(manifest.style.familyStyles[key]);
    }
}

fs.writeFileSync(manifestPath, JSON.stringify(manifest, null, 2));

if (manifest.style?.file) {
    const cssPath = path.resolve('public/build', manifest.style.file);
    if (fs.existsSync(cssPath)) {
        fs.writeFileSync(cssPath, rewrite(fs.readFileSync(cssPath, 'utf8')));
    }
}

console.log(`fix-fonts-base: rewrote /build/ -> ${base} in fonts manifest/css`);
