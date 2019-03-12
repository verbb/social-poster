<?php
namespace verbb\socialposter\variables;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\helpers\SocialPosterHelper;

use Craft;
use craft\fields\Assets;
use craft\web\View;

use yii\base\Behavior;

class SocialPosterVariable
{
    public function getPluginName()
    {
        return SocialPoster::$plugin->getPluginName();
    }

    public function getAssetFieldOptions()
    {
        return SocialPosterHelper::getAssetFieldOptions();
    }

}