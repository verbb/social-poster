<?php
namespace verbb\socialposter\models;

use verbb\socialposter\SocialPoster;

use craft\base\Model;
use craft\helpers\Json;

use LitEmoji\LitEmoji;

class Account extends Model
{
    // Properties
    // =========================================================================

    public $id;
    public $name;
    public $handle;
    public $enabled;
    public $autoPost;
    public $providerHandle;
    public $settings;
    public $sortOrder;
    public $tokenId;
    public $dateCreated;
    public $dateUpdated;


    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();

        if (is_string($this->settings)) {
            $this->settings = Json::decode($this->settings);
        }

        // Add Emoji support
        if (is_array($this->settings)) {
            foreach ($this->settings as $key => $value) {
                $this->settings[$key] = LitEmoji::shortcodeToUnicode($value);
            }
        }
    }

    public function rules()
    {
        return [
            [['id', 'sortOrder'], 'number', 'integerOnly' => true],
        ];
    }

    public function __toString(): string
    {
        return (string)$this->handle ?: static::class;
    }

    public function getIsNew(): bool
    {
        return (!$this->id || strpos($this->id, 'new') === 0);
    }

    public function getProviderIsConfigured(): bool
    {
        if ($this->getProvider()) {
            return true;
        }

        return false;
    }

    public function getProvider()
    {
        if ($this->providerHandle) {
            $provider = SocialPoster::$plugin->getProviders()->getProvider($this->providerHandle);

            if ($provider) {
                return $provider;
            }
        }

        return null;
    }

    public function getCanPost(): bool
    {
        if ($this->enabled && $this->provider->isConfigured() && $this->getToken()) {
            return true;
        }

        return false;
    }

    public function getToken()
    {
        if ($this->tokenId) {
            return SocialPoster::$plugin->getTokens()->getTokenById($this->tokenId);
        }

        return null;
    }

    public function getConnected()
    {
        return $this->getToken();
    }

}
