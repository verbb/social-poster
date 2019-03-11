<?php
namespace verbb\socialposter\providers;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\base\Provider;
use verbb\socialposter\helpers\SocialPosterHelper;
use verbb\socialposter\models\Account;

use Craft;

use League\OAuth2\Client\Provider\Facebook as FacebookProvider;

class Facebook extends Provider
{
    // Public Methods
    // =========================================================================

    public function getName(): string
    {
        return 'Facebook';
    }

    public function getSettingsHtml()
    {
        $assetFieldOptions = SocialPosterHelper::getAssetFieldOptions();

        return Craft::$app->getView()->renderTemplate('social-poster/_providers/facebook/settings', [
            'provider' => $this,
            'account' => $this->account,
            'assetFieldOptions' => $assetFieldOptions,
        ]);
    }

    public function getManagerUrl()
    {
        return 'https://developers.facebook.com/apps';
    }

    public function getScopeDocsUrl()
    {
        return 'https://developers.facebook.com/docs/facebook-login/permissions';
    }

    public function getOauthProviderConfig(): array
    {
        $config = parent::getOauthProviderConfig();

        if (empty($config['options']['graphApiVersion'])) {
            $config['graphApiVersion'] = 'v3.0';
        }

        return $config;
    }

    public function getOauthProvider(): FacebookProvider
    {
        $config = $this->getOauthProviderConfig();

        return new FacebookProvider($config['options']);
    }

    public function getResponseUrl($data)
    {
        if (isset($data['id'])) {
            return 'https://facebook.com/' . $data['id'];
        }
    }

}
