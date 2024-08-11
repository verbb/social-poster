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
    
    public ?string $pageId = null;


    // Public Methods
    // =========================================================================

    public function __construct(array $config = [])
    {
        unset($config['endpoint'], $config['groupId']);

        parent::__construct($config);
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
        } catch (Throwable $e) {
            self::apiError($this, $e);
        }

        return parent::fetchAccountSettings($settingsKey);
    }

    public function sendPost(Payload $payload): PostResponse
    {
        try {
            // Auth will deliver us a long-lived token, but if we're dealing with pages, we can generate
            // a never-expiring token.
            try {
                $response = $this->request('GET', $this->pageId, [
                    'query' => ['fields' => 'access_token'],
                ]);

                $pageAccessToken = $response['access_token'] ?? null;

                // Update the token in Auth to use this from now on.
                if ($pageAccessToken && $token = $this->getToken()) {
                    $token->accessToken = $pageAccessToken;

                        Auth::getInstance()->getTokens()->saveToken($token);
                }
            } catch (Throwable $e) {
                $this->getPostExceptionResponse($e);
            }

            $params = [
                'message' => $payload->message,
                'link' => $payload->url,
            ];

            // Only send the picture if there's content - otherwise will often fail due to API restrictions
            if ($payload->picture) {
                $params['picture'] = $payload->picture;
            }

            $response = $this->sendRequest($payload->element, $this->pageId . '/feed', $params);

            return $this->getPostResponse($response);
        } catch (Throwable $e) {
            return $this->getPostExceptionResponse($e);
        }
    }


    // Protected Methods
    // =========================================================================

    protected function defineRules(): array
    {
        $rules = parent::defineRules();

        $rules[] = [
            ['pageId'], 'required', 'when' => function($model) {
                return $model->enabled && $model->isConnected();
            },
        ];

        return $rules;
    }
}
