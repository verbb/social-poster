<?php
namespace verbb\socialposter\elements;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\elements\db\PostQuery;
use verbb\socialposter\models\Account;
use verbb\socialposter\records\Post as PostRecord;

use Craft;
use craft\base\Element;
use craft\elements\actions\Delete;
use craft\elements\db\ElementQueryInterface;
use craft\helpers\Json;
use craft\helpers\UrlHelper;

use yii\base\Exception;

use LitEmoji\LitEmoji;

class Post extends Element
{
    // Static Methods
    // =========================================================================

    public static function displayName(): string
    {
        return Craft::t('social-poster', 'Post');
    }

    public static function hasStatuses(): bool
    {
        return true;
    }

    public static function find(): PostQuery
    {
        return new PostQuery(static::class);
    }

    protected static function defineSources(string $context = null): array
    {
        $sources = [
            [
                'key' => '*',
                'label' => Craft::t('social-poster', 'All accounts'),
                'criteria' => [],
                'hasThumbs' => false,
                'defaultSort' => ['elements.dateCreated', 'desc'],
            ],
        ];

        $accounts = SocialPoster::$plugin->getAccounts()->getAllAccounts();

        if ($accounts) {
            $sources[] = ['heading' => Craft::t('social-poster', 'Accounts')];

            foreach ($accounts as $account) {
                $key = 'group:' . $account->handle;

                $sources[] = [
                    'key' => $key,
                    'label' => Craft::t('social-poster', $account->name),
                    'criteria' => ['accountId' => $account->id],
                    'hasThumbs' => false,
                    'defaultSort' => ['elements.dateCreated', 'desc'],
                ];
            }
        }

        return $sources;
    }

    protected static function defineSortOptions(): array
    {
        $attributes = [];
        $attributes['success'] = Craft::t('social-poster', 'Response');
        $attributes['provider'] = Craft::t('social-poster', 'Provider');
        $attributes['elements.dateCreated'] = Craft::t('social-poster', 'Date Posted');
        $attributes['elements.dateUpdated'] = Craft::t('social-poster', 'Date Updated');

        return $attributes;
    }

    protected static function defineTableAttributes(): array
    {
        $attributes = [];
        $attributes['title'] = ['label' => Craft::t('social-poster', 'Title')];
        $attributes['success'] = ['label' => Craft::t('social-poster', 'Response')];
        $attributes['provider'] = ['label' => Craft::t('social-poster', 'Provider')];
        $attributes['dateCreated'] = ['label' => Craft::t('social-poster', 'Date Posted')];
        $attributes['dateUpdated'] = ['label' => Craft::t('social-poster', 'Date Updated')];

        return $attributes;
    }

    protected static function defineDefaultTableAttributes(string $source): array
    {
        return ['title', 'dateCreated', 'success', 'provider', 'entry'];
    }

    protected static function defineActions(string $source = null): array
    {
        $actions = [];

        $actions[] = Craft::$app->getElements()->createAction([
            'type' => Delete::class,
            'confirmationMessage' => Craft::t('social-poster', 'Are you sure you want to delete the selected post?'),
            'successMessage' => Craft::t('social-poster', 'Posts deleted.'),
        ]);

        return $actions;
    }


    // Properties
    // =========================================================================

    public ?int $id = null;
    public ?int $accountId = null;
    public ?int $ownerId = null;
    public ?int $ownerSiteId = null;
    public ?string $ownerType = null;
    public ?array $settings = null;
    public ?bool $success = null;
    public ?array $response = null;
    public ?array $data = null;

    private mixed $_owner = null;


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
                $config['settings'] = [];
            }
        }

        if (array_key_exists('response', $config)) {
            if (is_string($config['response'])) {
                $config['response'] = Json::decodeIfJson($config['response']);
            }

            if (!is_array($config['response'])) {
                $config['response'] = [];
            }
        }

        if (array_key_exists('data', $config)) {
            if (is_string($config['data'])) {
                $config['data'] = Json::decodeIfJson($config['data']);
            }

            if (!is_array($config['data'])) {
                $config['data'] = [];
            }
        }

        parent::__construct($config);
    }

    public function init(): void
    {
        parent::init();

        if ($owner = $this->getOwner()) {
            $this->title = $owner->title;
        }

        // Add Emoji support
        if (is_array($this->settings)) {
            foreach ($this->settings as $key => $value) {
                $this->settings[$key] = LitEmoji::shortcodeToUnicode($value);
            }
        }
    }

    public function getOwner()
    {
        if ($this->_owner !== null) {
            return $this->_owner;
        }

        if ($this->ownerId === null) {
            return null;
        }

        return $this->_owner = Craft::$app->getElements()->getElementById($this->ownerId, $this->ownerType, $this->ownerSiteId);
    }

    public function setOwner($owner = null): void
    {
        $this->_owner = $owner;
    }

    public function getCpEditUrl(): ?string
    {
        return UrlHelper::cpUrl('social-poster/posts/' . $this->id);
    }

    public function getAccount(): ?Account
    {
        if ($this->accountId) {
            $account = SocialPoster::$plugin->getAccounts()->getAccountById($this->accountId);

            if ($account) {
                return $account;
            }
        }

        return null;
    }

    public function getProvider()
    {
        if ($account = $this->getAccount()) {
            return $account->getProvider();
        }

        return null;
    }

    public function afterSave(bool $isNew): void
    {
        if (!$isNew) {
            $record = PostRecord::findOne($this->id);

            if (!$record) {
                throw new Exception('Invalid post ID: ' . $this->id);
            }
        } else {
            $record = new PostRecord();
            $record->id = $this->id;
        }

        // Add Emoji support
        foreach ($this->settings as $key => $value) {
            $this->settings[$key] = LitEmoji::unicodeToShortcode($value);
        }

        $record->accountId = $this->accountId;
        $record->ownerId = $this->ownerId;
        $record->ownerSiteId = $this->ownerSiteId;
        $record->ownerType = $this->ownerType;
        $record->settings = $this->settings;
        $record->success = $this->success;
        $record->response = $this->response;
        $record->data = $this->data;

        $record->save(false);

        $this->id = $record->id;

        parent::afterSave($isNew);
    }


    // Protected Methods
    // =========================================================================

    protected function tableAttributeHtml(string $attribute): string
    {
        switch ($attribute) {
            case 'provider':
            {
                $provider = $this->getProvider();
                $account = $this->getProvider();

                if ($provider && $account) {
                    return '<span class="provider">' .
                        '<div class="thumb">' .
                        '<img width="20" src="' . $provider->getIconUrl() . '" class="social-icon social-' . $provider->handle . '" />' .
                        '</div>' . $account->name .
                        '</span>';
                }
            }
            case 'success':
            {
                if ($this->success) {
                    $message = $this->response['reasonPhrase'] ?? Craft::t('social-poster', 'Success');

                    return '<span class="status on"></span> ' . $message;
                }

                $message = $this->response['reasonPhrase'] ?? Craft::t('social-poster', 'Error');

                return '<span class="status off"></span> ' . $message;
            }
        }

        return parent::tableAttributeHtml($attribute);
    }
}
