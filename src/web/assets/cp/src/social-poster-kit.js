// ==========================================================================

// Social Poster Plugin for Craft CMS
// Author: Verbb - https://verbb.io/

// ==========================================================================

// Each component loads only when the page has one, so the Accounts page doesn't fetch the setup checklist.
if (document.querySelector('pk-setup-checklist')) {
    void Promise.all([
        import('@verbb/plugin-kit-web/plugin-kit.css'),
        import('@verbb/plugin-kit-web/components/setup-checklist.js'),
    ]);
}

if (document.querySelector('pk-state-panel')) {
    void Promise.all([
        import('@verbb/plugin-kit-web/plugin-kit.css'),
        import('@verbb/plugin-kit-web/components/state-panel.js'),
    ]);
}
