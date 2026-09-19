import { registerPluginBootstrap } from '@verbb/craft-screenshots/api';

import { ensureSocialPosterScreenshotModule } from './fixtures';

export default registerPluginBootstrap({
    id: 'social-poster',
    async setup(context) {
        await context.runCraft(['migrate/up', '--plugin=social-poster'], { allowFailure: true });
        await ensureSocialPosterScreenshotModule(context.installDir);
    },
});
