<?php
namespace verbb\socialposter\providers;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\base\Provider;
use verbb\socialposter\helpers\SocialPosterHelper;
use verbb\socialposter\models\Account;

use Craft;

use League\OAuth2\Client\Provider\LinkedIn as LinkedInProvider;

class LinkedIn extends Provider
{
    // Public Methods
    // =========================================================================

    public function getName(): string
    {
        return 'LinkedIn';
    }

    public function getSettingsHtml()
    {
        $assetFieldOptions = SocialPosterHelper::getAssetFieldOptions();

        return Craft::$app->getView()->renderTemplate('social-poster/_providers/linkedin/settings', [
            'provider' => $this,
            'account' => $this->account,
            'assetFieldOptions' => $assetFieldOptions,
        ]);
    }

    public function getDefaultOauthScope(): array
    {
        return [
            'r_basicprofile',
            'r_emailaddress'
        ];
    }

    public function getManagerUrl()
    {
        return 'https://www.linkedin.com/developer/apps';
    }

    public function getOauthProvider(): LinkedInProvider
    {
        $config = $this->getOauthProviderConfig();
        return new LinkedInProvider($config['options']);
    }

    public function getResponseUrl($data)
    {
        if (isset($data['updateUrl'])) {
            return $data['updateUrl'];
        }
    }

}
