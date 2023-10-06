<?php
namespace verbb\socialposter\accounts;

use verbb\socialposter\base\OAuthAccount;
use verbb\socialposter\models\Payload;
use verbb\socialposter\models\PostResponse;

use Throwable;

use verbb\auth\providers\LinkedIn as LinkedInProvider;

class LinkedIn extends OAuthAccount
{
    // Static Methods
    // =========================================================================

    public static function getOAuthProviderClass(): string
    {
        return LinkedInProvider::class;
    }


    // Properties
    // =========================================================================

    public static string $providerHandle = 'linkedIn';

    public ?string $endpoint = 'person';
    public ?string $organizationId = null;


    // Public Methods
    // =========================================================================

    public function defineRules(): array
    {
        $rules = parent::defineRules();

        $rules[] = [
            ['organizationId'], 'required', 'when' => function($model) {
                return $model->enabled && $model->endpoint === 'organization';
            },
        ];

        return $rules;
    }

    public function getOAuthProviderConfig(): array
    {
        $config = parent::getOAuthProviderConfig();

        // Use the "Posts API"
        // https://learn.microsoft.com/en-us/linkedin/marketing/integrations/community-management/shares/posts-api
        $config['restProtocolVersion'] = '2.0.0';
        $config['restVersion'] = '202309';

        // We need to reset the default scopes, because different ones are required depending on whether
        // posting to a personal page, or an organization. This is because the "Community Management API"
        // which is needed to post to an organisation can be the only product for a LinkedIn app.
        $config['defaultScopes'] = [];

        return $config;
    }

    public function getDefaultScopes(): array
    {
        // Posting to a personal page can be done with the "Share on LinkedIn" which doesn't require application.
        if ($this->endpoint === 'person') {
            return [
                'openid',
                'email',
                'profile',
                'w_member_social',
            ];
        }

        // Posting to a company page requires the "Community Management API" permission.
        if ($this->endpoint === 'organization') {
            return [
                'w_organization_social',
                'r_organization_social',
            ];
        }

        return [];
    }

    public function getResponseUrl($data): ?string
    {
        return $data['updateUrl'] ?? null;
    }

    public function sendPost(Payload $payload): PostResponse
    {
        try {
            $ownerUrn = '';
            $endpoint = $this->endpoint;

            if ($endpoint === 'person') {
                $response = $this->request('GET', 'userinfo');
                $profileId = $response['sub'] ?? null;

                $ownerUrn = 'urn:li:person:' . $profileId;  
            } else if ($endpoint === 'organization') {
                $ownerUrn = 'urn:li:organization:' . $this->organizationId;
            }

            $content = [
                'author' => $ownerUrn,
                'commentary' => $payload->message,
                'visibility' => 'PUBLIC',
                'lifecycleState' => 'PUBLISHED',
                'isReshareDisabledByAuthor' => false,
                'distribution' => [
                    'feedDistribution' => 'MAIN_FEED',
                    'targetEntities' => [],
                    'thirdPartyDistributionChannels' => [],
                ],
            ];

            // If we're sharing a URL, make it an article. Can only be either article or image though
            if ($payload->url && !$payload->picture) {
                $content['content']['article'] = [
                    'source' => $payload->url,
                    'title' => $payload->title,
                    'description' => $payload->message,
                ];
            }

            if ($payload->picture) {
                // Images are required to be uploaded first via 2 separate API calls.
                // Generate an asset request first, which will tell us where to upload and the Asset URN.
                $response = $this->request('POST', 'https://api.linkedin.com/rest/images', [
                    'query' => [
                        'action' => 'initializeUpload',
                    ],
                    'json' => [
                        'owner' => $ownerUrn,
                        'initializeUploadRequest' => [
                            'owner' => $ownerUrn,
                        ],
                    ],
                ]);

                $uploadUrl = $response['value']['uploadUrl'] ?? null;
                $imageUrn = $response['value']['image'] ?? null;

                if ($uploadUrl && $imageUrn) {
                    // Upload the image itself
                    $response = $this->request('POST', $uploadUrl, [
                        'body' => file_get_contents($payload->picture),
                    ]);

                    $content['content']['media']['id'] = $imageUrn;
                }
            }

            // We have to use the full URL here, the client will assume `api.linkedin.com/v2`.
            $response = $this->sendRequest($payload->element, 'https://api.linkedin.com/rest/posts', $content);

            return $this->getPostResponse($response);
        } catch (Throwable $e) {
            return $this->getPostExceptionResponse($e);
        }
    }

}
