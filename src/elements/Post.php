<?php
namespace verbb\socialposter\elements;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\base\AccountInterface;
use verbb\socialposter\elements\db\PostQuery;
use verbb\socialposter\records\Post as PostRecord;

use Craft;
use craft\base\Element;
use craft\elements\actions\Delete;
use craft\elements\User;
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
        $attributes['account'] = Craft::t('social-poster', 'Account');
        $attributes['elements.dateCreated'] = Craft::t('social-poster', 'Date Posted');
        $attributes['elements.dateUpdated'] = Craft::t('social-poster', 'Date Updated');

        return $attributes;
    }

    protected static function defineTableAttributes(): array
    {
        $attributes = [];
        $attributes['title'] = ['label' => Craft::t('social-poster', 'Title')];
        $attributes['success'] = ['label' => Craft::t('social-poster', 'Response')];
        $attributes['account'] = ['label' => Craft::t('social-poster', 'Account')];
        $attributes['dateCreated'] = ['label' => Craft::t('social-poster', 'Date Posted')];
        $attributes['dateUpdated'] = ['label' => Craft::t('social-poster', 'Date Updated')];

        return $attributes;
    }

    protected static function defineDefaultTableAttributes(string $source): array
    {
        return ['title', 'dateCreated', 'success', 'account', 'entry'];
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

    public function init(): void
    {
        parent::init();

        if ($owner = $this->getOwner()) {
            $this->title = $owner->title;
        }

        // Add Emoji support
        $this->settings = Json::decode(LitEmoji::shortcodeToUnicode(Json::encode($this->settings)));
        $this->response = Json::decode(LitEmoji::shortcodeToUnicode(Json::encode($this->response)));
        $this->data = Json::decode(LitEmoji::shortcodeToUnicode(Json::encode($this->data)));
    }

    public function canView(User $user): bool
    {
        return true;
    }

    public function canSave(User $user): bool
    {
        return true;
    }

    public function canDuplicate(User $user): bool
    {
        return true;
    }

    public function canDelete(User $user): bool
    {
        return true;
    }

    public function canCreateDrafts(User $user): bool
    {
        return true;
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

    public function getAccount(): ?AccountInterface
    {
        if ($this->accountId) {
            return SocialPoster::$plugin->getAccounts()->getAccountById($this->accountId);
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

        // Remove the recorded element from the payload, which is stored during posting for events
        unset($this->settings['element']);

        // Add Emoji support
        $this->settings = Json::decode(LitEmoji::unicodeToShortcode(Json::encode($this->settings)));
        $this->response = Json::decode(LitEmoji::unicodeToShortcode(Json::encode($this->response)));
        $this->data = Json::decode(LitEmoji::unicodeToShortcode(Json::encode($this->data)));

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
            case 'account':
            {
                if ($account = $this->getAccount()) {
                    $icon = '';

                    if ($account->icon) {
                        $icon = '<span class="sp-provider-icon">' . $account->icon . '</span>';
                    }

                    return '<div class="sp-provider" style="--bg-color: ' . $account->primaryColor . '">' .
                        $icon .
                        '<span class="sp-provider-label">' . $account->name . '</span>' .
                    '</div>';
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
