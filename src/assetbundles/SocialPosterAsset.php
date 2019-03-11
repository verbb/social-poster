<?php
namespace verbb\socialposter\assetbundles;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class SocialPosterAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init()
    {
        $this->sourcePath = "@verbb/socialposter/resources/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->css = [
            'css/social-poster.css',
        ];

        $this->js = [
            'js/social-poster.js',
        ];

        parent::init();
    }
}
