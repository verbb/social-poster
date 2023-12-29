<?php
namespace verbb\socialposter\base;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\events\SendPostEvent;
use verbb\socialposter\helpers\SocialPosterHelper;
use verbb\socialposter\models\Payload;
use verbb\socialposter\models\PostResponse;

use Craft;
use craft\base\ElementInterface;
use craft\base\SavableComponent;
use craft\helpers\Db;
use craft\helpers\Json;
use craft\helpers\StringHelper;
use craft\validators\HandleValidator;

use verbb\auth\helpers\Provider as ProviderHelper;

use Exception;

use GuzzleHttp\Exception\RequestException;

abstract class Account extends SavableComponent implements AccountInterface
{
    // Constants
    // =========================================================================

    public const EVENT_BEFORE_SEND_POST = 'beforeSendPost';
    public const EVENT_AFTER_SEND_POST = 'afterSendPost';


    // Static Methods
    // =========================================================================

    public static function log($account, $message, $throwError = false): void
    {
        SocialPoster::info($account->name . ': ' . $message);

        if ($throwError) {
            throw new Exception($message);
        }
    }

    public static function error($account, $message, $throwError = false): void
    {
        SocialPoster::error($account->name . ': ' . $message);

        if ($throwError) {
            throw new Exception($message);
        }
    }

    public static function apiError($account, $exception, $throwError = true): void
    {
        $messageText = $exception->getMessage();

        // Check for Guzzle errors, which are truncated in the exception `getMessage()`.
        if ($exception instanceof RequestException && $exception->getResponse()) {
            $messageText = (string)$exception->getResponse()->getBody();
        }

        $message = Craft::t('social-poster', 'API error: “{message}” {file}:{line}', [
            'message' => $messageText,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);

        SocialPoster::error($account->name . ': ' . $message);

        if ($throwError) {
            throw new Exception($message);
        }
    }


    // Properties
    // =========================================================================

    public ?string $name = null;
    public ?string $handle = null;
    public ?bool $enabled = null;
    public ?bool $autoPost = null;
    public ?int $sortOrder = null;
    public array $cache = [];

    public ?string $showTitle = null;
    public ?string $title = null;
    public ?string $showUrl = null;
    public ?string $url = null;
    public ?string $showMessage = null;
    public ?string $message = null;
    public ?string $showImageField = null;
    public ?string $imageField = null;
    
    // Set via config files
    public array $authorizationOptions = [];
    public array $scopes = [];


    // Abstract Methods
    // =========================================================================

    abstract public static function getOAuthProviderClass(): string;
    abstract public function sendPost(Payload $payload): PostResponse;


    // Public Methods
    // =========================================================================

    public function __construct(array $config = [])
    {
        // Config normalization
        if (array_key_exists('defaultUrl', $config)) {
            unset($config['defaultUrl']);
        }

        parent::__construct($config);
    }

    public function init(): void
    {
        parent::init();

        // Add Emoji support
        foreach ($this->getSettings() as $key => $value) {
            if ($value && is_string($value)) {
                $this->$key = StringHelper::shortcodesToEmoji($value);
            }
        }
    }

    public function settingsAttributes(): array
    {
        $attributes = parent::settingsAttributes();
        $attributes[] = 'showTitle';
        $attributes[] = 'title';
        $attributes[] = 'showUrl';
        $attributes[] = 'clientId';
        $attributes[] = 'url';
        $attributes[] = 'showMessage';
        $attributes[] = 'message';
        $attributes[] = 'showImageField';
        $attributes[] = 'imageField';

        return $attributes;
    }

    public function getProviderName(): string
    {
        return static::displayName();
    }

    public function getPrimaryColor(): ?string
    {
        return ProviderHelper::getPrimaryColor(static::$providerHandle);
    }

    public function getIcon(): ?string
    {
        return ProviderHelper::getIcon(static::$providerHandle);
    }

    public function isConnected(): bool
    {
        return false;
    }

    public function getAccountSettings(string $settingsKey, bool $useCache = true): ?array
    {
        if ($useCache) {
            // Return even if empty, we don't want to force setting the value unless told to
            return $this->getDataCache($settingsKey);
        }

        $settings = $this->fetchAccountSettings($settingsKey);

        if ($settings) {
            $this->setDataCache([$settingsKey => $settings]);
        }

        return $settings;
    }

    public function fetchAccountSettings(string $settingsKey): ?array
    {
        return [];
    }

    public function getSettingsHtml(): ?string
    {
        $handle = StringHelper::toKebabCase(static::$providerHandle);

        return Craft::$app->getView()->renderTemplate('social-poster/accounts/_types/' . $handle . '/settings', [
            'account' => $this,
        ]);
    }

    public function getPostSettingsHtml(): ?string
    {
        $handle = StringHelper::toKebabCase(static::$providerHandle);

        $assetFieldOptions = SocialPosterHelper::getAssetFieldOptions();

        return Craft::$app->getView()->renderTemplate('social-poster/accounts/_types/' . $handle . '/post-settings', [
            'account' => $this,
            'assetFieldOptions' => $assetFieldOptions,
        ]);
    }

    public function getInputHtml($context): string
    {
        $variables = $context;
        $variables['account'] = $this;
        $variables['assetFieldOptions'] = SocialPosterHelper::getAssetFieldOptions();

        $handle = StringHelper::toKebabCase(static::$providerHandle);

        return Craft::$app->getView()->renderTemplate('social-poster/accounts/_types/' . $handle . '/input', $variables);
    }

    public function getAssetFieldOptions(): array
    {
        return SocialPosterHelper::getAssetFieldOptions();
    }

    public function sendRequest(ElementInterface $element, string $endpoint, mixed $payload, string $method = 'POST', string $contentType = 'json'): mixed
    {
        // Allow events to cancel sending
        if (!$this->beforeSendPost($element, $endpoint, $payload, $method)) {
            return false;
        }

        $response = $this->request($method, $endpoint, [
            $contentType => $payload,
        ]);

        // Allow events to say the response is invalid
        if (!$this->afterSendPost($element, $endpoint, $payload, $method, $response)) {
            return false;
        }

        return $response;
    }

    public function beforeSendPost(ElementInterface $element, string &$endpoint, mixed &$payload, string &$method): bool
    {
        $event = new SendPostEvent([
            'element' => $element,
            'payload' => $payload,
            'endpoint' => $endpoint,
            'method' => $method,
            'account' => $this,
        ]);
        $this->trigger(self::EVENT_BEFORE_SEND_POST, $event);

        if (!$event->isValid) {
            self::info($this, 'Sending post cancelled by event hook.');
        }

        // Allow events to alter some props
        $payload = $event->payload;
        $endpoint = $event->endpoint;
        $method = $event->method;

        return $event->isValid;
    }

    public function afterSendPost(ElementInterface $element, string $endpoint, mixed $payload, string $method, mixed $response): bool
    {
        $event = new SendPostEvent([
            'element' => $element,
            'payload' => $payload,
            'response' => $response,
            'endpoint' => $endpoint,
            'method' => $method,
            'account' => $this,
        ]);
        $this->trigger(self::EVENT_AFTER_SEND_POST, $event);

        if (!$event->isValid) {
            self::info($this, 'Post marked as invalid by event hook.');
        }

        return $event->isValid;
    }


    // Protected Methods
    // =========================================================================

    protected function defineRules(): array
    {
        $rules = parent::defineRules();

        $rules[] = [['name', 'handle'], 'required'];
        $rules[] = [['id', 'sortOrder'], 'number', 'integerOnly' => true];

        $rules[] = [
            ['handle'],
            HandleValidator::class,
            'reservedWords' => [
                'dateCreated',
                'dateUpdated',
                'edit',
                'id',
                'title',
                'uid',
            ],
        ];

        return $rules;
    }

    protected function getPostExceptionResponse(mixed $exception): PostResponse
    {
        $statusCode = '[error]';
        $data = [];
        $reasonPhrase = $exception->getMessage();

        // Check for Guzzle errors, which are truncated in the exception `getMessage()`.
        if ($exception instanceof RequestException && $response = $exception->getResponse()) {
            $statusCode = $response->getStatusCode();
            $reasonPhrase = $response->getReasonPhrase();

            $data = Json::decode((string)$response->getBody());
        }

        // Save more detail to the log file
        SocialPoster::error('Error posting to {account}: “{message}” {file}:{line}', [
            'account' => $this->name,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);

        return new PostResponse([
            'success' => false,
            'data' => $data,
            'response' => [
                'statusCode' => $statusCode,
                'reasonPhrase' => $reasonPhrase,
            ],
        ]);
    }

    protected function getPostResponse(mixed $response): PostResponse
    {
        return new PostResponse([
            'success' => true,
            'data' => $response,
        ]);
    }


    // Private Methods
    // =========================================================================

    private function setDataCache(array $values): void
    {
        $this->cache = array_merge($this->cache, $values);

        $data = Json::encode($this->cache);

        // Direct DB update to keep it out of PC, plus speed
        Db::update('{{%socialposter_accounts}}', ['cache' => $data], ['id' => $this->id]);
    }

    private function getDataCache(string $key): mixed
    {
        return $this->cache[$key] ?? null;
    }
}
