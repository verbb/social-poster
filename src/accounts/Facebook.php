<?php
namespace verbb\socialposter\accounts;

use verbb\socialposter\base\OAuthAccount;
use verbb\socialposter\models\Payload;
use verbb\socialposter\models\PostResponse;

use Throwable;

use verbb\auth\Auth;
use verbb\auth\providers\Facebook as FacebookProvider;

class Facebook extends OAuthAccount
{
    // Static Methods
    // =========================================================================

    public static function getOAuthProviderClass(): string
    {
        return FacebookProvider::class;
    }

    
    // Properties
    // =========================================================================

    public static string $providerHandle = 'facebook';
    
    public ?string $endpoint = null;
    public ?string $groupId = null;
    public ?string $pageId = null;


    // Public Methods
    // =========================================================================

    public function defineRules(): array
    {
        $rules = parent::defineRules();

        $rules[] = [
            ['endpoint'], 'required', 'when' => function($model) {
                return $model->enabled;
            },
        ];

        $rules[] = [
            ['groupId'], 'required', 'when' => function($model) {
                return $model->enabled && $model->endpoint === 'group' && $model->isConnected();
            },
        ];

        $rules[] = [
            ['pageId'], 'required', 'when' => function($model) {
                return $model->enabled && $model->endpoint === 'page' && $model->isConnected();
            },
        ];

        return $rules;
    }

    public function getOAuthProviderConfig(): array
    {
        $config = parent::getOAuthProviderConfig();
        $config['graphApiVersion'] = 'v15.0';

        return $config;
    }

    public function getDefaultScopes(): array
    {
        return [
            // API version 7.0+
            'public_profile',
            'email',
            'pages_manage_posts',
            'publish_to_groups',
            'pages_read_engagement',
            'pages_read_user_content',
            'pages_show_list',
        ];
    }

    public function getResponseUrl($data): ?string
    {
        if (isset($data['id'])) {
            return 'https://facebook.com/' . $data['id'];
        }

        return null;
    }

    public function fetchAccountSettings(string $settingsKey): ?array
    {
        try {
            if ($settingsKey === 'pageId') {
                $pages = [];

                $response = $this->request('GET', 'me/accounts');
                $accounts = $response['data'] ?? [];

                foreach ($accounts as $account) {
                    $pages[] = [
                        'label' => $account['name'] ?? null,
                        'value' => $account['id'] ?? null,
                    ];
                }

                return $pages;
            }

            if ($settingsKey === 'groupId') {
                $pages = [];

                $response = $this->request('GET', 'me/groups');
                $accounts = $response['data'] ?? [];

                foreach ($accounts as $account) {
                    $pages[] = [
                        'label' => $account['name'] ?? null,
                        'value' => $account['id'] ?? null,
                    ];
                }

                return $pages;
            }
        } catch (Throwable $e) {
            self::apiError($this, $e);
        }

        return parent::fetchAccountSettings($settingsKey);
    }

    public function sendPost(Payload $payload): PostResponse
    {
        try {
            $pageOrGroupId = '';
            $endpoint = $this->endpoint;

            if ($endpoint == 'page') {
                $pageOrGroupId = $this->pageId;
            } else if ($endpoint == 'group') {
                $pageOrGroupId = $this->groupId;
            }

            // Auth will deliver us a long-lived token, but if we're dealing with pages, we can generate
            // a never-expiring token.
            if ($endpoint == 'page') {
                // This will fail if not a page (Business or Group) so catch and continue
                try {
                    $response = $this->request('GET', $pageOrGroupId, [
                        'query' => ['fields' => 'access_token'],
                    ]);

                    $pageAccessToken = $response['access_token'] ?? null;

                    // Update the token in Auth to use this from now on.
                    if ($pageAccessToken && $token = $this->getToken()) {
                        $token->accessToken = $pageAccessToken;

                        Auth::$plugin->getTokens()->saveToken($token);
                    }
                } catch (Throwable $e) {
                    $this->getPostExceptionResponse($e);
                }
            }

            $params = [
                'message' => $payload->message,
                'link' => $payload->url,
            ];

            // Only send the picture if there's content - otherwise will often fail due to API restrictions
            if ($payload->picture) {
                $params['picture'] = $payload->picture;
            }

            $response = $this->sendRequest($payload->element, $pageOrGroupId . '/feed', $params);

            return $this->getPostResponse($response);
        } catch (Throwable $e) {
            return $this->getPostExceptionResponse($e);
        }
    }
}
