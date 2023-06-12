<?php
namespace verbb\socialposter\accounts;

use verbb\socialposter\base\OAuthAccount;
use verbb\socialposter\models\Payload;
use verbb\socialposter\models\PostResponse;

use Exception;
use Throwable;

use verbb\auth\providers\Facebook as InstagramProvider;

class Instagram extends OAuthAccount
{
    // Static Methods
    // =========================================================================

    public static function getOAuthProviderClass(): string
    {
        return InstagramProvider::class;
    }

    
    // Properties
    // =========================================================================

    public static string $providerHandle = 'instagram';

    public ?string $pageId = null;


    // Public Methods
    // =========================================================================

    public function defineRules(): array
    {
        $rules = parent::defineRules();

        $rules[] = [
            ['pageId'], 'required', 'when' => function($model) {
                return $model->enabled && $model->isConnected();
            },
        ];

        $rules[] = [
            ['imageField'], 'required', 'when' => function($model) {
                return $model->enabled;
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

    public function getAuthorizationUrlOptions(): array
    {
        return [
            'scope' => [
                'instagram_basic',
                'pages_show_list',
            ],
        ];
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
            // From the Page ID, fetch the Instagram Business ID
            $response = $this->request('GET', $this->pageId, [
                'query' => ['fields' => 'instagram_business_account'],
            ]);

            $instagramAccountId = $response['instagram_business_account']['id'] ?? null;

            if (!$instagramAccountId) {
                throw new Exception('Unable to find Instagram Business ID for page "' . $this->pageId .'". Ensure Instagram has access to this page.');
            }

            // Then, create our media container. Image is compulsary
            $response = $this->sendRequest($payload->element, "$instagramAccountId/media", [
                'image_url' => $payload->picture,
                'caption' => $payload->message,
            ], 'POST', 'query');

            $mediaObjectContainerId = $response['id'] ?? null;

            if (!$mediaObjectContainerId) {
                throw new Exception('Unable to create media container.');
            }

            $response = $this->sendRequest($payload->element, "$instagramAccountId/media_publish", [
                'creation_id' => $mediaObjectContainerId,
            ], 'POST', 'query');

            $mediaId = $response['id'] ?? null;

            if (!$mediaId) {
                throw new Exception('Unable to create media.');
            }

            return $this->getPostResponse($response);
        } catch (Throwable $e) {
            return $this->getPostExceptionResponse($e);
        }
    }
}
