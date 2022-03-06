<?php
namespace verbb\socialposter\models;

use verbb\socialposter\SocialPoster;

use craft\base\Model;

use League\OAuth1\Client\Credentials\TokenCredentials;
use League\OAuth2\Client\Token\AccessToken;

use DateTime;

class Token extends Model
{
    // Properties
    // =========================================================================

    public ?int $id = null;
    public ?string $providerHandle = null;
    public ?string $accessToken = null;
    public ?string $secret = null;
    public ?string $endOfLife = null;
    public ?string $refreshToken = null;
    public ?DateTime $dateCreated = null;
    public ?DateTime $dateUpdated = null;


    // Public Methods
    // =========================================================================

    public function getProvider()
    {
        return SocialPoster::$plugin->getProviders()->getProvider($this->providerHandle);
    }

    public function getToken(): AccessToken|TokenCredentials|null
    {
        if ($provider = $this->getProvider()) {
            switch ($provider->oauthVersion()) {
                case 1: {
                    $realToken = new TokenCredentials();
                    $realToken->setIdentifier($this->accessToken);
                    $realToken->setSecret($this->secret);

                    return $realToken;
                }
                case 2: {
                    return new AccessToken([
                        'access_token' => $this->accessToken,
                        'refresh_token' => $this->refreshToken,
                        'secret' => $this->secret,
                        'expires' => $this->endOfLife,
                    ]);
                }
            }
        }

        return null;
    }
}
