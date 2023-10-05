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

    public function getDefaultScopes(): array
    {
        $scopes = [
            'w_member_social',
        ];

        if ($this->endpoint == 'organization') {
            $scopes[] = 'w_organization_social';
            $scopes[] = 'r_organization_social';
        }

        return $scopes;
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
                $response = $this->request('GET', 'me');
                $profileId = $response['id'] ?? null;

                $ownerUrn = 'urn:li:person:' . $profileId;
            } else if ($endpoint === 'organization') {
                $ownerUrn = 'urn:li:organization:' . $this->organizationId;
            }

            $thumbnails = [];

            if ($payload->picture) {
                $thumbnails[] = [
                    'resolvedUrl' => $payload->picture,
                ];
            }

            $response = $this->sendRequest($payload->element, 'shares', [
                'content' => [
                    'contentEntities' => [
                        [
                            'entityLocation' => $payload->url,
                            'thumbnails' => $thumbnails,
                        ],
                    ],
                    'title' => $payload->title,
                ],
                'owner' => $ownerUrn,
                'subject' => $payload->title,
                'text' => [
                    'text' => $payload->message,
                ],
                'distribution' => [
                    'linkedInDistributionTarget' => [
                        'visibleToGuest' => true,
                    ],
                ],
            ]);

            return $this->getPostResponse($response);
        } catch (Throwable $e) {
            return $this->getPostExceptionResponse($e);
        }
    }

}
