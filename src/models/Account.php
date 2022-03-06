<?php
namespace verbb\socialposter\models;

use verbb\socialposter\SocialPoster;

use craft\base\Model;
use craft\helpers\Json;

use LitEmoji\LitEmoji;
use DateTime;

class Account extends Model
{
    // Properties
    // =========================================================================

    public ?int $id = null;
    public ?string $name = null;
    public ?string $handle = null;
    public ?bool $enabled = null;
    public ?bool $autoPost = null;
    public ?string $providerHandle = null;
    public ?array $settings = null;
    public ?int $sortOrder = null;
    public ?int $tokenId = null;
    public ?DateTime $dateCreated = null;
    public ?DateTime $dateUpdated = null;


    // Public Methods
    // =========================================================================

    public function __construct($config = [])
    {
        // Config normalization
        if (array_key_exists('settings', $config)) {
            if (is_string($config['settings'])) {
                $config['settings'] = Json::decodeIfJson($config['settings']);
            }

            if (!is_array($config['settings'])) {
                unset($config['settings']);
            }
        }

        parent::__construct($config);
    }

    public function init(): void
    {
        parent::init();

        // Add Emoji support
        if (is_array($this->settings)) {
            foreach ($this->settings as $key => $value) {
                $this->settings[$key] = LitEmoji::shortcodeToUnicode($value);
            }
        }
    }

    public function rules(): array
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
        return (!$this->id || str_starts_with($this->id, 'new'));
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
        return $this->enabled && $this->getProvider()->isConfigured() && $this->getToken();
    }

    public function getToken(): ?Token
    {
        if ($this->tokenId) {
            return SocialPoster::$plugin->getTokens()->getTokenById($this->tokenId);
        }

        return null;
    }

    public function getConnected(): ?Token
    {
        return $this->getToken();
    }

}
