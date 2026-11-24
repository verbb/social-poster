// ==========================================================================

// Social Poster Plugin for Craft CMS
// Author: Verbb - https://verbb.io/

// ==========================================================================

// Plugin Kit's stylesheet loads with the page, in <head>, so its FOUCE rules hide pk-* elements until their components load.
import '@verbb/plugin-kit-web/plugin-kit.css';

// Each component loads only when the page has one, so the Accounts page doesn't fetch the setup checklist.
if (document.querySelector('pk-setup-checklist')) {
    void import('@verbb/plugin-kit-web/components/setup-checklist.js');
}

if (document.querySelector('pk-state-panel')) {
    void import('@verbb/plugin-kit-web/components/state-panel.js');
}
