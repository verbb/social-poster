<?php
namespace verbb\socialposter\controllers;

use verbb\socialposter\SocialPoster;

use craft\web\Controller;

use yii\web\Response;

class PluginController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionSettings(): Response
    {
        $settings = SocialPoster::$plugin->getSettings();

        return $this->renderTemplate('social-poster/settings', array(
            'settings' => $settings,
        ));
    }

}