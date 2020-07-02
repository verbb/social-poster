<?php
namespace verbb\socialposter\providers;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\base\Provider;
use verbb\socialposter\helpers\SocialPosterHelper;
use verbb\socialposter\models\Account;

use Craft;
use craft\helpers\Json;

use League\OAuth2\Client\Provider\LinkedIn as LinkedInProvider;

class LinkedInAlt extends LinkedIn
{
    // Public Methods
    // =========================================================================

    public function getName(): string
    {
        return 'LinkedIn (Alt)';
    }

    public function getHandle(): string
    {
        return 'linkedinAlt';
    }

    public function getIconUrl()
    {
        return Craft::$app->assetManager->getPublishedUrl('@verbb/socialposter/resources/dist/img/linkedin.svg', true);
    }

    public function getInputHtml($context)
    {
        $variables = $context;
        $variables['provider'] = $this;

        return Craft::$app->getView()->renderTemplate('social-poster/_providers/linkedin-alt/input', $variables);
    }

    public function getSettingsHtml()
    {
        $assetFieldOptions = SocialPosterHelper::getAssetFieldOptions();

        return Craft::$app->getView()->renderTemplate('social-poster/_providers/linkedin-alt/settings', [
            'provider' => $this,
            'account' => $this->account,
            'assetFieldOptions' => $assetFieldOptions,
        ]);
    }

}
