<?php
namespace verbb\socialposter\controllers;

use verbb\socialposter\SocialPoster;

use Craft;
use craft\web\Controller;

class PluginController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionSettings()
    {
        $settings = SocialPoster::$plugin->getSettings();

        $this->renderTemplate('social-poster/settings', array(
            'settings' => $settings,
        ));
    }

}