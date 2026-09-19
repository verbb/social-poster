import { defineScreenshotScenario } from '@verbb/craft-screenshots/api';

import { seedSocialPosterFixture } from '../../support/fixtures';

let entryEditRoute = '/admin/entries';

export default defineScreenshotScenario({
    id: 'social-poster-feature-tour-composer',
    output: 'feature-tour/social-poster-composer.png',
    route: () => entryEditRoute,
    viewport: { width: 1180, height: 920, deviceScaleFactor: 2 },
    async setup(context) {
        const fixture = await seedSocialPosterFixture(context, 'composer');
        entryEditRoute = fixture.entryEditRoute;
    },
    waitFor: [
        { type: 'loadState', state: 'networkidle' },
        { type: 'selector', selector: '.sp-sidebar', state: 'visible', timeout: 30000 },
        { type: 'selector', selector: '.sp-sidebar textarea', state: 'visible', timeout: 30000 },
    ],
    steps: [{ type: 'wait', waitFor: { type: 'timeout', ms: 300 } }],
    target: {
        type: 'selector',
        selector: '.sp-sidebar',
        padding: { top: 4, right: 4, bottom: 4, left: 4 },
    },
    caption: 'Social Poster’s real Craft 5 entry widget with Facebook and Twitter accounts ready to publish.',
    intent: 'Show per-account tabs, publishing controls, message defaults and image selection in the genuine plugin UI.',
});
