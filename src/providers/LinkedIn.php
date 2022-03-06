<?php
namespace verbb\socialposter\providers;

use verbb\socialposter\base\Provider;
use verbb\socialposter\helpers\SocialPosterHelper;

use Craft;
use craft\helpers\Json;

use League\OAuth2\Client\Provider\LinkedIn as LinkedInProvider;

use Throwable;

use GuzzleHttp\Client;

class LinkedIn extends Provider
{
    // Public Methods
    // =========================================================================

    public function getName(): string
    {
        return 'LinkedIn';
    }

    public function getSettingsHtml(): ?string
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
        $scopes = [
            'r_liteprofile',
            'r_emailaddress',
            'w_member_social',
        ];

        $endpoint = $this->account->settings['endpoint'] ?? '';

        if ($endpoint == 'organization') {
            $scopes[] = 'w_organization_social';
            $scopes[] = 'r_organization_social';
        }

        return $scopes;
    }

    public function getManagerUrl(): ?string
    {
        return 'https://www.linkedin.com/developer/apps';
    }

    public function getOauthProvider(): LinkedInProvider
    {
        $config = $this->getOauthProviderConfig();
        return new LinkedInProvider($config['options']);
    }

    public function getResponseUrl($data): ?string
    {
        return $data['updateUrl'] ?? null;
    }

    public function sendPost($account, $content): array
    {
        try {
            $token = $account->getToken();
            $client = $this->getClient($token);

            $ownerUrn = '';
            $endpoint = $content['endpoint'] ?? 'person';

            if ($endpoint === 'person') {
                $urn = $this->getProfile($client)['id'];

                $ownerUrn = 'urn:li:person:' . $urn;
            } else if ($endpoint === 'organization') {
                $urn = $content['organizationId'] ?? '';

                $ownerUrn = 'urn:li:organization:' . $urn;
            }

            $thumbnails = [];

            if (isset($content['picture']) && $content['picture']) {
                $thumbnails[] = [
                    'resolvedUrl' => $content['picture'],
                ];
            }

            $response = $client->post('shares', [
                'json' => [
                    'content' => [
                        'contentEntities' => [
                            [
                                'entityLocation' => $content['url'],
                                'thumbnails' => $thumbnails,
                            ]
                        ],
                        'title' => $content['title'],
                    ],
                    'owner' => $ownerUrn,
                    'subject' => $content['title'],
                    'text' => [
                        'text' => $content['message'],
                    ],
                    'distribution' => [
                        'linkedInDistributionTarget' => [
                            'visibleToGuest' => true,
                        ],
                    ],
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
