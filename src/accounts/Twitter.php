<?php
namespace verbb\socialposter\accounts;

use verbb\socialposter\base\OAuthAccount;
use verbb\socialposter\models\Payload;
use verbb\socialposter\models\PostResponse;

use Throwable;

use verbb\auth\providers\Twitter as TwitterProvider;

class Twitter extends OAuthAccount
{
    // Static Methods
    // =========================================================================

    public static function getOAuthProviderClass(): string
    {
        return TwitterProvider::class;
    }


    // Properties
    // =========================================================================

    public static string $providerHandle = 'twitter';


    // Public Methods
    // =========================================================================

    public function getAuthorizationUrlOptions(): array
    {
        return [
            'scope' => [
                'tweet.read',
                'tweet.write',
                'users.read',
            ],
        ];
    }

    public function getResponseUrl($data): ?string
    {
        return null;
    }

    public function sendPost(Payload $payload): PostResponse
    {
        try {
            $params = [
                'text' => $payload->message,
            ];

            $response = $this->request('POST', 'tweets', [
                'json' => $params,
            ]);

            return $this->getPostResponse($response);
        } catch (Throwable $e) {
            return $this->getPostExceptionResponse($e);
        }
    }
}
