import { defineScreenshotScenario } from '@verbb/craft-screenshots/api';

import { seedSocialPosterFixture } from '../../support/fixtures';

let entryEditRoute = '/admin/entries';

export default defineScreenshotScenario({
    id: 'social-poster-feature-tour-success',
    output: 'feature-tour/social-poster-success.png',
    route: () => entryEditRoute,
    viewport: { width: 1180, height: 760, deviceScaleFactor: 2 },
    async setup(context) {
        const fixture = await seedSocialPosterFixture(context, 'success');
        entryEditRoute = fixture.entryEditRoute;
    },
    waitFor: [
        { type: 'loadState', state: 'networkidle' },
        { type: 'selector', selector: '.sp-sidebar .warning', state: 'visible', timeout: 30000 },
    ],
    steps: [{ type: 'wait', waitFor: { type: 'timeout', ms: 250 } }],
    target: {
        type: 'selector',
        selector: '.sp-sidebar',
        padding: { top: 4, right: 4, bottom: 4, left: 4 },
    },
    caption: 'A successful Facebook post recorded in Social Poster’s real entry widget.',
    intent: 'Show the previous posting time and deliberate Post again action without contacting a remote provider.',
});
