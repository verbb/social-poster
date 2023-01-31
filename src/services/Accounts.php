<?php
namespace verbb\socialposter\services;

use verbb\socialposter\accounts as accountTypes;
use verbb\socialposter\base\AccountInterface;
use verbb\socialposter\elements\Post;
use verbb\socialposter\events\AccountEvent;
use verbb\socialposter\records\Account as AccountRecord;

use Craft;
use craft\base\MemoizableArray;
use craft\db\Query;
use craft\errors\MissingComponentException;
use craft\events\RegisterComponentTypesEvent;
use craft\helpers\Component as ComponentHelper;
use craft\helpers\Json;

use yii\base\Component;
use yii\base\InvalidConfigException;

use LitEmoji\LitEmoji;

use Exception;
use Throwable;

class Accounts extends Component
{
    // Constants
    // =========================================================================

    public const EVENT_REGISTER_ACCOUNT_TYPES = 'registerAccountTypes';
    public const EVENT_BEFORE_SAVE_ACCOUNT = 'beforeSaveAccount';
    public const EVENT_AFTER_SAVE_ACCOUNT = 'afterSaveAccount';
    public const EVENT_BEFORE_DELETE_ACCOUNT = 'beforeDeleteAccount';
    public const EVENT_AFTER_DELETE_ACCOUNT = 'afterDeleteAccount';


    // Properties
    // =========================================================================

    private ?MemoizableArray $_accounts = null;
    private ?array $_overrides = null;


    // Public Methods
    // =========================================================================

    public function getAllAccountTypes(): array
    {
        $accountTypes = [
            accountTypes\Facebook::class,
            accountTypes\Instagram::class,
            accountTypes\LinkedIn::class,
            accountTypes\Twitter::class,
        ];

        $event = new RegisterComponentTypesEvent([
            'types' => $accountTypes,
        ]);

        $this->trigger(self::EVENT_REGISTER_ACCOUNT_TYPES, $event);

        return $event->types;
    }

    public function createAccount(mixed $config): AccountInterface
    {
        $handle = $config['handle'] ?? null;
        $settings = $config['settings'] ?? [];

        // Allow config settings to override account settings
        if ($handle && $settings) {
            $configOverrides = $this->getAccountOverrides($handle);

            if ($configOverrides) {
                if (is_string($settings)) {
                    $settings = Json::decode($settings);
                }

                $config['settings'] = array_merge($settings, $configOverrides);
            }
        }                

        try {
            return ComponentHelper::createComponent($config, AccountInterface::class);
        } catch (MissingComponentException|InvalidConfigException $e) {
            $config['errorMessage'] = $e->getMessage();
            $config['expectedType'] = $config['type'];
            unset($config['type']);
            return new accountTypes\MissingAccount($config);
        }
    }

    public function getAllAccounts(): array
    {
        return $this->_accounts()->all();
    }

    public function getAllEnabledAccounts(): array
    {
        return $this->_accounts()->where('enabled', true)->all();
    }

    public function getAllConfiguredAccounts(): array
    {
        $accounts = [];

        foreach ($this->getAllEnabledAccounts() as $account) {
            if ($account->isConfigured()) {
                $accounts[] = $account;
            }
        }

        return $accounts;
    }

    public function getAccountById(int $id): ?AccountInterface
    {
        return $this->_accounts()->firstWhere('id', $id);
    }

    public function getAccountByHandle(string $handle): ?AccountInterface
    {
        return $this->_accounts()->firstWhere('handle', $handle, true);
    }

    public function saveAccount(AccountInterface $account, bool $runValidation = true): bool
    {
        $isNewAccount = !$account->id;

        // Fire a 'beforeSaveAccount' event
        if ($this->hasEventHandlers(self::EVENT_BEFORE_SAVE_ACCOUNT)) {
            $this->trigger(self::EVENT_BEFORE_SAVE_ACCOUNT, new AccountEvent([
                'account' => $account,
                'isNew' => $isNewAccount,
            ]));
        }

        if ($runValidation && !$account->validate()) {
            Craft::info('Account not saved due to validation error.', __METHOD__);
            return false;
        }

        // Ensure we support Emoji's properly
        $settings = $account->settings;

        foreach ($settings as $key => $value) {
            if ($value && is_string($value)) {
                $settings[$key] = LitEmoji::unicodeToShortcode($value);
            }
        }

        $accountRecord = $this->_getAccountRecordById($account->id);
        $accountRecord->name = $account->name;
        $accountRecord->handle = $account->handle;
        $accountRecord->enabled = $account->enabled;
        $accountRecord->autoPost = $account->autoPost;
        $accountRecord->type = get_class($account);
        $accountRecord->settings = $settings;

        if ($isNewAccount) {
            $maxSortOrder = (new Query())
                ->from(['{{%socialposter_accounts}}'])
                ->max('[[sortOrder]]');

            $accountRecord->sortOrder = $maxSortOrder ? $maxSortOrder + 1 : 1;
        }

        $accountRecord->save(false);

        if (!$account->id) {
            $account->id = $accountRecord->id;
        }

        // Fire an 'afterSaveAccount' event
        if ($this->hasEventHandlers(self::EVENT_AFTER_SAVE_ACCOUNT)) {
            $this->trigger(self::EVENT_AFTER_SAVE_ACCOUNT, new AccountEvent([
                'account' => $account,
                'isNew' => $isNewAccount,
            ]));
        }

        return true;
    }

    public function reorderAccounts(array $accountIds): bool
    {
        $transaction = Craft::$app->getDb()->beginTransaction();

        try {
            foreach ($accountIds as $accountOrder => $accountId) {
                $accountRecord = $this->_getAccountRecordById($accountId);
                $accountRecord->sortOrder = $accountOrder + 1;
                $accountRecord->save();
            }

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();

            throw $e;
        }

        return true;
    }

    public function getAccountOverrides(string $handle): array
    {
        if ($this->_overrides === null) {
            $this->_overrides = Craft::$app->getConfig()->getConfigFromFile('social-poster');
        }

        return $this->_overrides['accounts'][$handle] ?? [];
    }

    public function deleteAccountById(int $accountId): bool
    {
        $account = $this->getAccountById($accountId);

        if (!$account) {
            return false;
        }

        return $this->deleteAccount($account);
    }

    public function deleteAccount(AccountInterface $account): bool
    {
        // Fire a 'beforeDeleteAccount' event
        if ($this->hasEventHandlers(self::EVENT_BEFORE_DELETE_ACCOUNT)) {
            $this->trigger(self::EVENT_BEFORE_DELETE_ACCOUNT, new AccountEvent([
                'account' => $account,
            ]));
        }

        $db = Craft::$app->getDb();
        $transaction = $db->beginTransaction();
        $elementsService = Craft::$app->getElements();

        try {
            $posts = Post::find()
                ->status(null)
                ->accountId($account->id)
                ->all();

            foreach ($posts as $post) {
                $elementsService->deleteElement($post);
            }

            $db->createCommand()
                ->delete('{{%socialposter_accounts}}', ['id' => $account->id])
                ->execute();

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();

            throw $e;
        }

        // Fire an 'afterDeleteAccount' event
        if ($this->hasEventHandlers(self::EVENT_AFTER_DELETE_ACCOUNT)) {
            $this->trigger(self::EVENT_AFTER_DELETE_ACCOUNT, new AccountEvent([
                'account' => $account,
            ]));
        }

        // Clear caches
        $this->_accounts = null;

        return true;
    }


    // Private Methods
    // =========================================================================

    private function _accounts(): MemoizableArray
    {
        if (!isset($this->_accounts)) {
            $accounts = [];

            foreach ($this->_createAccountQuery()->all() as $result) {
                $accounts[] = $this->createAccount($result);
            }

            $this->_accounts = new MemoizableArray($accounts);
        }

        return $this->_accounts;
    }

    private function _createAccountQuery(): Query
    {
        return (new Query())
            ->select([
                'id',
                'name',
                'handle',
                'enabled',
                'autoPost',
                'type',
                'settings',
                'sortOrder',
                'cache',
                'dateCreated',
                'dateUpdated',
            ])
            ->from(['{{%socialposter_accounts}}'])
            ->orderBy(['sortOrder' => SORT_ASC]);
    }

    private function _getAccountRecordById(int $accountId = null): ?AccountRecord
    {
        if ($accountId !== null) {
            $accountRecord = AccountRecord::findOne(['id' => $accountId]);

            if (!$accountRecord) {
                throw new Exception(Craft::t('social-poster', 'No account exists with the ID “{id}”.', ['id' => $accountId]));
            }
        } else {
            $accountRecord = new AccountRecord();
        }

        return $accountRecord;
    }

}
