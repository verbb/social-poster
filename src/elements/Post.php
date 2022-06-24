<?php
namespace verbb\socialposter\elements;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\elements\db\PostQuery;
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
    // Static
    // =========================================================================

    public static function displayName(): string
    {
        return Craft::t('social-poster', 'Post');
    }

    public static function hasStatuses(): bool
    {
        return true;
    }

    public static function find(): ElementQueryInterface
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
        $attributes['success'] = Craft::t('social-poster', 'Response');
        $attributes['provider'] = Craft::t('social-poster', 'Provider');
        $attributes['elements.dateCreated'] = Craft::t('social-poster', 'Date Posted');
        $attributes['elements.dateUpdated'] = Craft::t('social-poster', 'Date Updated');

        return $attributes;
    }

    protected static function defineTableAttributes(): array
    {
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

    public $id;
    public $accountId;
    public $ownerId;
    public $ownerSiteId;
    public $ownerType;
    public $settings;
    public $success;
    public $response;
    public $data;

    private $_owner;


    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();

        if ($owner = $this->getOwner()) {
            $this->title = $owner->title;
        }

        $this->settings = Json::decode($this->settings, true);
        $this->response = Json::decode($this->response, true);
        $this->data = Json::decode($this->data, true);

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

    public function setOwner($owner = null)
    {
        $this->_owner = $owner;
    }

    public function getCpEditUrl()
    {
        return UrlHelper::cpUrl('social-poster/posts/' . $this->id);
    }

    public function getAccount()
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


    // Indexes, etc.
    // -------------------------------------------------------------------------

    protected function tableAttributeHtml(string $attribute): string
    {
        switch ($attribute) {
            case 'provider':
            {
                $provider = $this->getProvider();
                $account = $this->getProvider();

                if ($provider && $account) {
                    $html = '<span class="provider">' .
                        '<div class="thumb">' .
                        '<img width="20" src="' . $provider->getIconUrl() . '" class="social-icon social-' . $provider->handle . '" />' .
                        '</div>' . $account->name .
                        '</span>';

                    return $html;
                }
            }
            case 'success':
            {
                if ($this->success) {
                    $message = $this->response['reasonPhrase'] ?? Craft::t('social-poster', 'Success');

                    return '<span class="status on"></span> ' . $message;
                } else {
                    $message = $this->response['reasonPhrase'] ?? Craft::t('social-poster', 'Error');

                    return '<span class="status off"></span> ' . $message;
                }
            }
        }

        return parent::tableAttributeHtml($attribute);
    }


    // Events
    // -------------------------------------------------------------------------

    public function afterSave(bool $isNew)
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
}
