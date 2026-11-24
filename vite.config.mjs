import { resolve } from 'node:path';

import { defineConfig } from 'vite';

const root = resolve(import.meta.dirname, 'src/web/assets/cp');

export default defineConfig(({ mode }) => {
    // Plugin Kit's components are an ES module with lazy chunks, published apart from the main IIFE bundle.
    if (mode === 'kit') {
        return {
            root,
            // Relative, so lazy chunks resolve beside the entry wherever Craft publishes it.
            base: '',
            build: {
                outDir: resolve(root, 'dist/kit'),
                emptyOutDir: true,
                assetsDir: '',
                minify: 'oxc',
                sourcemap: false,
                rolldownOptions: {
                    input: resolve(root, 'src/social-poster-kit.js'),
                    output: {
                        entryFileNames: '[name].js',
                        chunkFileNames: '[name]-[hash].js',
                        assetFileNames: '[name]-[hash][extname]',
                        format: 'es',
                    },
                },
            },
        };
    }

    const isCssBuild = mode === 'css';

    if (!isCssBuild && mode !== 'js') {
        throw new Error(`Unknown asset build mode: ${mode}`);
    }

    let output;

    if (isCssBuild) {
        output = {
            assetFileNames: '[name][extname]',
        };
    } else {
        output = {
            codeSplitting: false,
            entryFileNames: '[name].js',
            format: 'iife',
        };
    }

    return {
        root,
        input: resolve(root, `src/social-poster.${isCssBuild ? 'css' : 'js'}`),
        build: {
            outDir: resolve(root, 'dist'),
            emptyOutDir: isCssBuild,
            assetsDir: '',
            cssMinify: 'esbuild',
            cssTarget: ['chrome61', 'safari10'],
            minify: 'oxc',
            sourcemap: !isCssBuild,
            target: 'es2015',
            rolldownOptions: {
                output,
            },
        },
    };
});
