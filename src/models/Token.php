<?php
namespace verbb\socialposter\models;

use verbb\socialposter\SocialPoster;

use craft\base\Model;

use League\OAuth1\Client\Credentials\TokenCredentials;
use League\OAuth2\Client\Token\AccessToken;

class Token extends Model
{
    // Public Properties
    // =========================================================================

    public $id;
    public $providerHandle;
    public $accessToken;
    public $secret;
    public $endOfLife;
    public $refreshToken;
    public $dateCreated;
    public $dateUpdated;


    // Public Methods
    // =========================================================================

    public function getProvider()
    {
        return SocialPoster::$plugin->getProviders()->getProvider($this->providerHandle);
    }

    public function getToken()
    {
        $provider = $this->getProvider();

        if ($provider) {
            switch ($provider->oauthVersion()) {
                case 1: {
                    $realToken = new TokenCredentials();
                    $realToken->setIdentifier($response['identifier']);
                    $realToken->setSecret($response['secret']);

                    return $realToken;

                    // return $provider->createAccessToken([
                    //     'identifier' => $this->accessToken,
                    //     'secret' => $this->secret,
                    // ]);
                }
                case 2: {
                    $realToken = new AccessToken([
                        'access_token' => $this->accessToken,
                        'refresh_token' => $this->refreshToken,
                        'secret' => $this->secret,
                        'expires' => $this->endOfLife,
                    ]);

                    return $realToken;
                }
            }
        }
    }
}
