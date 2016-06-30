<?php
namespace Craft;

class SocialPoster_PluginController extends BaseController
{
    // Public Methods
    // =========================================================================

    public function actionCheckRequirements()
    {
        $dependencies = craft()->socialPoster_plugin->checkRequirements();

        if ($dependencies) {
            $this->renderTemplate('socialposter/_special/dependencies', array(
                'dependencies' => $dependencies,
            ));
        }
    }
}

