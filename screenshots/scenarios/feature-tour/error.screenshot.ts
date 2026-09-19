import { defineScreenshotScenario } from '@verbb/craft-screenshots/api';

import { seedSocialPosterFixture } from '../../support/fixtures';

let entryEditRoute = '/admin/entries';

export default defineScreenshotScenario({
    id: 'social-poster-feature-tour-error',
    output: 'feature-tour/social-poster-error.png',
    route: () => entryEditRoute,
    viewport: { width: 1180, height: 760, deviceScaleFactor: 2 },
    async setup(context) {
        const fixture = await seedSocialPosterFixture(context, 'error');
        entryEditRoute = fixture.entryEditRoute;
    },
    waitFor: [
        { type: 'loadState', state: 'networkidle' },
        { type: 'selector', selector: '.sp-sidebar .error', state: 'visible', timeout: 30000 },
    ],
    steps: [{ type: 'wait', waitFor: { type: 'timeout', ms: 250 } }],
    target: {
        type: 'selector',
        selector: '.sp-sidebar',
        padding: { top: 4, right: 4, bottom: 4, left: 4 },
    },
    caption: 'A failed provider response shown in Social Poster’s genuine entry widget.',
    intent: 'Show account-specific failure status, useful response details and the real retry action.',
});
