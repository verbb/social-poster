<?php
namespace verbb\socialposter\providers;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\base\Provider;
use verbb\socialposter\helpers\SocialPosterHelper;
use verbb\socialposter\models\Account;

use Craft;
use craft\helpers\Json;

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
            'r_liteprofile',
            'r_emailaddress',
            'w_share',
            'w_member_social',
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

    public function sendPost($account, $content)
    {
        try {
            $token = $account->getToken();
            $client = $this->getClient($token);

            $personURN = $this->getProfile($client)['id'];

            $response = $client->post('ugcPosts', [
                'json' => [
                    'author' => 'urn:li:person:' . $personURN,
                    'lifecycleState' => 'PUBLISHED',
                    'specificContent' => [
                        'com.linkedin.ugc.ShareContent' => [
                            'shareCommentary' => [
                                'text' =>  $content['message'],
                            ],
                            'shareMediaCategory' => 'NONE',
                        ],
                    ],
                    'visibility' => [
                        'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
                    ],
                ],
            ]);

            return $this->getPostResponse($response);
        } catch (\Throwable $e) {
            return $this->getPostExceptionResponse($e);
        }
    }


    // Private Methods
    // =========================================================================

    private function getClient($token)
    {
        return Craft::createGuzzleClient([
            'base_uri' => 'https://api.linkedin.com/v2/',
            'headers' => [
                'Authorization' => 'Bearer ' . $token->accessToken,
                'Connection' => 'Keep-Alive',
                'Content-Type' => 'application/json',
                // 'x-li-format' => 'json',
                'X-Restli-Protocol-Version' => '2.0.0',
                // 'x-li-src' => 'msdk',
            ],
        ]);
    }

    private function getProfile($client)
    {
        $response = $client->get('me');

        return Json::decode((string)$response->getBody());
    }

}
