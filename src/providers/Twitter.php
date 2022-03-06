<?php
namespace verbb\socialposter\providers;

use verbb\socialposter\base\Provider;
use verbb\socialposter\helpers\SocialPosterHelper;

use Craft;

use League\OAuth1\Client\Server\Twitter as TwitterProvider;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

use Throwable;

class Twitter extends Provider
{
    // Public Methods
    // =========================================================================

    public function getName(): string
    {
        return 'Twitter';
    }

    public function getSettingsHtml(): ?string
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

    public function getManagerUrl(): ?string
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

    public function getResponseUrl($data): ?string
    {
        if (isset($data['id'])) {
            return 'https://twitter.com/' . $data['user']['screen_name'] . '/status/' . $data['id'];
        }

        return null;
    }

    public function sendPost($account, $content): array
    {
        try {
            $token = $account->getToken();
            $client = $this->getClient($token);

            $response = $client->post('statuses/update.json', [
                'form_params' => [
                    'status' => $content['message'],
                ],
            ]);

            return $this->getPostResponse($response);
        } catch (Throwable $e) {
            return $this->getPostExceptionResponse($e);
        }
    }


    // Private Methods
    // =========================================================================

    private function getClient($token): Client
    {
        $info = $this->getOauthProviderConfig();

        $stack = HandlerStack::create();

        $stack->push(new Oauth1([
            'consumer_key' => $info['options']['clientId'],
            'consumer_secret' => $info['options']['clientSecret'],
            'token' => $token->accessToken,
            'token_secret' => $token->secret,
        ]));

        return Craft::createGuzzleClient([
            'base_uri' => 'https://api.twitter.com/1.1/',
            'handler' => $stack,
            'auth' => 'oauth',
        ]);
    }
}
