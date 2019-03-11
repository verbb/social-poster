<?php
namespace verbb\socialposter\providers;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\base\Provider;
use verbb\socialposter\helpers\SocialPosterHelper;
use verbb\socialposter\models\Account;

use Craft;

use League\OAuth1\Client\Server\Twitter as TwitterProvider;

class Twitter extends Provider
{
    // Public Methods
    // =========================================================================

    public function getName(): string
    {
        return 'Twitter';
    }

    public function getSettingsHtml()
    {
        $assetFieldOptions = SocialPosterHelper::getAssetFieldOptions();

        return Craft::$app->getView()->renderTemplate('social-poster/_providers/twitter/settings', [
            'provider' => $this,
            'account' => $this->account,
            'assetFieldOptions' => $assetFieldOptions,
        ]);
    }

    public function oauthVersion(): int
    {
        return 1;
    }

    public function getManagerUrl()
    {
        return 'https://dev.twitter.com/apps';
    }

    public function getOauthProvider(): TwitterProvider
    {
        $config = $this->getOauthProviderConfig();

        $config['identifier'] = $config['options']['clientId'] ?? '';
        unset($config['options']['clientId']);

        $config['secret'] = $config['options']['clientSecret'] ?? '';
        unset($config['options']['clientSecret']);

        $config['callback_uri'] = $config['options']['redirectUri'] ?? '';
        unset($config['options']['redirectUri']);

        return new TwitterProvider($config);
    }

    public function getResponseUrl($data)
    {
        if (isset($data['id'])) {
            return 'https://twitter.com/' . $data['user']['screen_name'] . '/status/' . $data['id'];
        }
    }
}
