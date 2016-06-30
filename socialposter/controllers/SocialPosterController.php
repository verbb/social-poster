<?php
namespace Craft;

class SocialPosterController extends BaseController
{
    // Public Methods
    // =========================================================================

    //
    // Control Panel
    //

    public function actionSettings()
    {
        $settings = craft()->socialPoster->getSettings();

        $this->renderTemplate('socialposter/settings', array(
            'settings' => $settings,
        ));
    }
}
