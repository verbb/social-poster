<?php
namespace verbb\socialposter\web\assets\cp;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class SocialPosterKitAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        // Plugin Kit components, kept apart from the main bundle so only the pages that show them load them.
        $this->sourcePath = '@verbb/socialposter/web/assets/cp/dist/kit';

        $this->depends = [
            CpAsset::class,
        ];

        // Plugin Kit's stylesheet, in <head> so its FOUCE rules hide pk-* elements until their components load.
        $this->css = [
            'social-poster-kit.css',
        ];

        $this->js = [
            'social-poster-kit.js',
        ];

        // An ES module, so its components load as chunks beside it.
        $this->jsOptions = [
            'type' => 'module',
        ];

        parent::init();
    }
}
