import { defineScreenshotScenario } from '@verbb/craft-screenshots/api';

import { seedSocialPosterFixture } from '../../support/fixtures';

let postsIndexRoute = '/admin/social-poster/posts';

export default defineScreenshotScenario({
    id: 'social-poster-feature-tour-posts-index',
    output: 'feature-tour/social-poster-posts-index.png',
    route: () => postsIndexRoute,
    viewport: { width: 1440, height: 900, deviceScaleFactor: 2 },
    async setup(context) {
        const fixture = await seedSocialPosterFixture(context, 'index');
        postsIndexRoute = fixture.postsIndexRoute;
    },
    waitFor: [
        { type: 'loadState', state: 'networkidle' },
        { type: 'selector', selector: '.elements', state: 'visible', timeout: 30000 },
    ],
    steps: [
        {
            type: 'evaluate',
            expression: `
                const elements = document.querySelector('.elements');

                if (elements instanceof HTMLElement) {
                    elements.style.marginLeft = '24px';
                    elements.style.width = 'calc(100% - 24px)';
                }
            `,
        },
        { type: 'wait', waitFor: { type: 'timeout', ms: 500 } },
    ],
    target: {
        type: 'selector',
        selector: '.elements',
        padding: { top: 0, right: 4, bottom: 4, left: 24 },
    },
    caption: 'Social Poster’s real Craft 5 post index with successful and failed posts across four accounts.',
    intent: 'Show the searchable posting history, provider identity, response status and date in the genuine plugin element index.',
});
