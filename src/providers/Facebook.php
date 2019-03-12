<?php
namespace verbb\socialposter\providers;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\base\Provider;
use verbb\socialposter\helpers\SocialPosterHelper;
use verbb\socialposter\models\Account;

use Craft;
use craft\helpers\Json;

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

    public function getDefaultOauthScope(): array
    {
        return [
            // 'publish_actions',
            'publish_pages',
            'publish_to_groups',
            'manage_pages',
            // 'user_managed_groups',
            'user_posts',
            'user_photos',
        ];
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
            $config['options']['graphApiVersion'] = 'v3.0';
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

    public function sendPost($account, $content)
    {
        try {
            $token = $account->getToken();
            $info = $this->getOauthProviderConfig();

            $pageOrGroupId = '';
            $endpoint = $content['endpoint'];
            $accessToken = $token->accessToken;

            if ($endpoint == 'page') {
                $pageOrGroupId = $endpoint = $content['pageId'];
            } else if ($endpoint == 'group') {
                $pageOrGroupId = $endpoint = $content['groupId'];
            }

            $fb = new \Facebook\Facebook([
                'app_id' => $info['options']['clientId'],
                'app_secret' => $info['options']['clientSecret'],
                'default_graph_version' => 'v2.10',
            ]);

            $client = Craft::createGuzzleClient([
                'base_uri' => 'https://graph.facebook.com/',
            ]);

            if ($pageOrGroupId) {
                // Get long-lived access token from the user access token
                $accessToken = $fb->getOAuth2Client()->getLongLivedAccessToken($accessToken);
                $fb->setDefaultAccessToken($accessToken);
            
                // Use long-lived access token to get a page access token which will never expire
                $response = $fb->sendRequest('GET', $pageOrGroupId, ['fields' => 'access_token'])->getDecodedBody();
                $accessToken = $response['access_token'];
                $fb->setDefaultAccessToken($accessToken);
            }

            $response = $client->post($endpoint . '/feed', [
                'form_params' => [
                    'access_token' => $accessToken,
                    'message' => $content['message'],
                    'link' => $content['url'],
                    // 'name' => $content['title'],
                ]
            ]);

            return $this->getPostResponse($response);
        } catch (\Throwable $e) {
            return $this->getPostExceptionResponse($e);
        }
    }
}
