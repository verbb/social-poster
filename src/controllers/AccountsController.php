<?php
namespace verbb\socialposter\controllers;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\base\AccountInterface;
use verbb\socialposter\records\Account as AccountRecord;

use Craft;
use craft\helpers\ArrayHelper;
use craft\helpers\Json;
use craft\web\Controller;

use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use verbb\base\helpers\EnvironmentSettings;
use verbb\base\models\EnvironmentSettingsViolation;

class AccountsController extends Controller
{
    // Public Methods
    // =========================================================================

    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $this->requireCpRequest();
        $this->requirePermission('socialPoster-accounts');

        return true;
    }

    public function actionIndex(): Response
    {
        $accounts = SocialPoster::$plugin->getAccounts()->getAllAccounts();

        return $this->renderTemplate('social-poster/accounts', [
            'accounts' => $accounts,
        ]);
    }

    public function actionEdit(?string $handle = null, AccountInterface $account = null): Response
    {
        $accountsService = SocialPoster::$plugin->getAccounts();

        if ($account === null) {
            if ($handle !== null) {
                $account = $accountsService->getAccountByHandle($handle);

                if ($account === null) {
                    throw new NotFoundHttpException('Account not found');
                }
            }
        }

        $allAccountTypes = $accountsService->getAllAccountTypes();

        $accountInstances = [];
        $accountOptions = [];

        foreach ($allAccountTypes as $accountType) {
            /** @var AccountInterface $accountInstance */
            $accountInstance = Craft::createObject($accountType);

            if ($account === null) {
                $account = $accountInstance;
            }

            $accountInstances[$accountType] = $accountInstance;

            $accountOptions[] = [
                'value' => $accountType,
                'label' => $accountInstance::displayName(),
            ];
        }

        // Sort them by name
        ArrayHelper::multisort($accountOptions, 'label');

        if ($handle && $accountsService->getAccountByHandle($handle)) {
            $title = trim($account->name) ?: Craft::t('social-poster', 'Edit Account');
        } else {
            $title = Craft::t('social-poster', 'Create a new account');
        }

        return $this->renderTemplate('social-poster/accounts/_edit', [
            'title' => $title,
            'account' => $account,
            'accountOptions' => $accountOptions,
            'accountInstances' => $accountInstances,
            'accountTypes' => $allAccountTypes,
        ]);
    }

    public function actionSave(): ?Response
    {
        $this->requirePostRequest();

        $accountsService = SocialPoster::$plugin->getAccounts();
        $accountId = $this->request->getParam('accountId') ?: null;
        $type = $this->request->getParam('type');

        if ($accountId) {
            $oldAccount = $accountsService->getAccountById($accountId);

            if (!$oldAccount) {
                throw new BadRequestHttpException("Invalid account ID: $accountId");
            }
        }

        $handle = $this->request->getParam('handle');
        $settings = $this->request->getParam("types.$type");

        $account = $accountsService->createAccount([
            'id' => $accountId,
            'type' => $type,
            'name' => $this->request->getParam('name'),
            'handle' => $handle,
            'enabled' => (bool)$this->request->getParam('enabled'),
            'autoPost' => (bool)$this->request->getParam('autoPost'),
            'settings' => $settings,
        ]);

        if ($violation = $this->_environmentSettingsViolation($account, $handle, $settings)) {
            $message = $violation->reason === EnvironmentSettingsViolation::REFERENCE
                ? Craft::t('social-poster', 'Only admins can use environment variables here.')
                : Craft::t('social-poster', 'Enter the credentials again after changing where this connects.');

            $account->addError($violation->getAttribute() ?? 'clientId', $message);

            return $this->asModelFailure($account, Craft::t('social-poster', 'Couldn’t save account.'), 'account');
        }

        if (!$accountsService->saveAccount($account)) {
            return $this->asModelFailure($account, Craft::t('social-poster', 'Couldn’t save account.'), 'account');
        }

        return $this->asModelSuccess($account, Craft::t('social-poster', 'Account saved.'), 'account');
    }

    public function actionReorder(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $accountIds = Json::decode($this->request->getRequiredBodyParam('ids'));
        SocialPoster::$plugin->getAccounts()->reorderAccounts($accountIds);

        return $this->asSuccess();
    }

    public function actionDelete(): Response
    {
        $this->requirePostRequest();

        $accountId = $this->request->getRequiredBodyParam('id');

        SocialPoster::$plugin->getAccounts()->deleteAccountById($accountId);

        return $this->asSuccess();
    }

    public function actionRefreshSettings(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $accountsService = SocialPoster::$plugin->getAccounts();

        $accountHandle = $this->request->getRequiredBodyParam('account');
        $setting = $this->request->getRequiredBodyParam('setting');

        $account = $accountsService->getAccountByHandle($accountHandle);

        if (!$account) {
            throw new BadRequestHttpException("Invalid account: $accountHandle");
        }

        return $this->asJson($account->getAccountSettings($setting, false));
    }


    // Private Methods
    // =========================================================================

    private function _environmentSettingsViolation(AccountInterface $account, mixed $handle, mixed $settings): ?EnvironmentSettingsViolation
    {
        // Read the settings the way `createAccount()` merges them into the account.
        if (is_string($settings)) {
            $settings = Json::decodeIfJson($settings);
        }

        $posted = is_array($settings) ? $settings : [];

        // Config file settings replace posted values in `createAccount()`, so the user can't change them here.
        $overrides = is_string($handle) && $handle !== '' ? SocialPoster::$plugin->getAccounts()->getAccountOverrides($handle) : [];
        $posted = array_diff_key($posted, $overrides);
        $credentials = array_values(array_diff(['clientId', 'clientSecret'], array_keys($overrides)));

        // Compare against what's saved in the database, as loaded accounts already include config file settings.
        $record = $account->id ? AccountRecord::findOne(['id' => $account->id]) : null;
        $stored = $record ? Json::decodeIfJson($record->settings ?? '') : [];
        $providerChanged = $record && $record->type !== get_class($account);

        // Provider hosts are fixed, so only a change of provider can send saved credentials somewhere new.
        return EnvironmentSettings::violation($posted, is_array($stored) ? $stored : [], $credentials, false, $providerChanged, []);
    }
}
