<?php
namespace verbb\socialposter\assetbundles;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

use verbb\base\assetbundles\CpAsset as VerbbCpAsset;

class SocialPosterAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->sourcePath = "@verbb/socialposter/resources/dist";

        $this->depends = [
            VerbbCpAsset::class,
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
