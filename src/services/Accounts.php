<?php
namespace verbb\socialposter\services;

use verbb\socialposter\elements\Post;
use verbb\socialposter\events\AccountEvent;
use verbb\socialposter\models\Account;
use verbb\socialposter\records\Account as AccountRecord;

use Craft;
use craft\db\Query;

use yii\base\Component;

use LitEmoji\LitEmoji;
use Exception;
use Throwable;

class Accounts extends Component
{
    // Constants
    // =========================================================================

    public const EVENT_BEFORE_SAVE_ACCOUNT = 'beforeSaveAccount';
    public const EVENT_AFTER_SAVE_ACCOUNT = 'afterSaveAccount';
    public const EVENT_BEFORE_DELETE_ACCOUNT = 'beforeDeleteAccount';
    public const EVENT_AFTER_DELETE_ACCOUNT = 'afterDeleteAccount';


    // Properties
    // =========================================================================

    private ?array $_accountsById = null;
    private bool $_fetchedAllAccounts = false;
    private ?array $_overrides = null;


    // Public Methods
    // =========================================================================

    public function getAllAccounts(): array
    {
        if ($this->_fetchedAllAccounts) {
            return array_values($this->_accountsById);
        }

        $this->_accountsById = [];

        foreach ($this->_createAccountQuery()->all() as $result) {
            $account = new Account($result);
            $this->_accountsById[$account->id] = $account;
        }

        $this->_fetchedAllAccounts = true;

        return array_values($this->_accountsById);
    }

    public function getAccountById($id): ?Account
    {
        $result = $this->_createAccountQuery()
            ->where(['id' => $id])
            ->one();

        return $result ? new Account($result) : null;
    }

    public function getAccountByHandle($handle): ?Account
    {
        $result = $this->_createAccountQuery()
            ->where(['handle' => $handle])
            ->one();

        return $result ? new Account($result) : null;
    }

    public function saveAccount(Account $account, bool $runValidation = true): bool
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
        foreach ($account->settings as $key => $value) {
            $account->settings[$key] = LitEmoji::unicodeToShortcode($value);
        }

        $accountRecord = $this->_getAccountRecordById($account->id);
        $accountRecord->name = $account->name;
        $accountRecord->handle = $account->handle;
        $accountRecord->enabled = $account->enabled;
        $accountRecord->autoPost = $account->autoPost;
        $accountRecord->providerHandle = $account->providerHandle;
        $accountRecord->settings = $account->settings;
        $accountRecord->tokenId = $account->tokenId;

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

    public function getAccountOverrides(string $handle)
    {
        if ($this->_overrides === null) {
            $this->_overrides = Craft::$app->getConfig()->getConfigFromFile('accounts');
        }

        return $this->_overrides[$handle] ?? null;
    }

    public function deleteAccountById(int $accountId): bool
    {
        $account = $this->getAccountById($accountId);

        if (!$account) {
            return false;
        }

        return $this->deleteAccount($account);
    }

    public function deleteAccount(Account $account): bool
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
                ->anyStatus()
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

        return true;
    }


    // Private Methods
    // =========================================================================

    private function _createAccountQuery(): Query
    {
        return (new Query())
            ->select([
                'id',
                'name',
                'handle',
                'enabled',
                'autoPost',
                'providerHandle',
                'settings',
                'sortOrder',
                'tokenId',
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
