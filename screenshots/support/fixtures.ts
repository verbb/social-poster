import { readFileSync } from 'node:fs';
import { copyFile, mkdir, readFile, writeFile } from 'node:fs/promises';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

import type { ScreenshotSetupContext } from '@verbb/craft-screenshots/types';

export type SocialPosterState = 'composer' | 'success' | 'error' | 'index';

type SocialPosterFixture = {
    entryEditRoute: string;
    postsIndexRoute: string;
};

const supportDir = dirname(fileURLToPath(import.meta.url));
const seedScript = readFileSync(join(supportDir, 'seed', 'seed-social-poster.php'), 'utf8');

/** Seed a real entry, connected accounts and the requested posting state. */
export async function seedSocialPosterFixture(
    context: ScreenshotSetupContext,
    state: SocialPosterState,
): Promise<SocialPosterFixture> {
    await ensureSocialPosterScreenshotModule(context.installDir);

    const output = await context.runCraftScript(
        `$screenshotState = ${JSON.stringify(state)};\n${seedScript}`,
        { label: `seed-social-poster-${state}` },
    );
    const fixture = JSON.parse(output.trim()) as SocialPosterFixture;

    if (!fixture.entryEditRoute) {
        throw new Error(`Invalid Social Poster fixture payload: ${output}`);
    }

    return fixture;
}

/** Register screenshot-only connected account types in the generated Craft app. */
export async function ensureSocialPosterScreenshotModule(installDir: string): Promise<void> {
    const moduleDir = join(installDir, 'modules/socialposterscreenshots');
    await mkdir(moduleDir, { recursive: true });

    for (const filename of [
        'Module.php',
        'ScreenshotFacebookAccount.php',
        'ScreenshotInstagramAccount.php',
        'ScreenshotLinkedInAccount.php',
        'ScreenshotTwitterAccount.php',
    ]) {
        await copyFile(join(supportDir, 'module', filename), join(moduleDir, filename));
    }

    const appPath = join(installDir, 'config/app.php');
    let contents = await readFile(appPath, 'utf8');

    if (contents.includes("'socialPosterScreenshots'")) {
        return;
    }

    contents = contents.replace(
        /return\s*\[\s*'id'\s*=>\s*App::env\('CRAFT_APP_ID'\)\s*\?:\s*'CraftCMS',\s*\];/s,
        `require_once dirname(__DIR__) . '/modules/socialposterscreenshots/Module.php';
require_once dirname(__DIR__) . '/modules/socialposterscreenshots/ScreenshotFacebookAccount.php';
require_once dirname(__DIR__) . '/modules/socialposterscreenshots/ScreenshotInstagramAccount.php';
require_once dirname(__DIR__) . '/modules/socialposterscreenshots/ScreenshotLinkedInAccount.php';
require_once dirname(__DIR__) . '/modules/socialposterscreenshots/ScreenshotTwitterAccount.php';

return [
    'id' => App::env('CRAFT_APP_ID') ?: 'CraftCMS',
    'modules' => [
        'socialPosterScreenshots' => \\modules\\socialposterscreenshots\\Module::class,
    ],
    'bootstrap' => ['socialPosterScreenshots'],
];`,
    );

    if (!contents.includes("'socialPosterScreenshots'")) {
        throw new Error('Failed to register the Social Poster screenshot module.');
    }

    await writeFile(appPath, contents);
}
