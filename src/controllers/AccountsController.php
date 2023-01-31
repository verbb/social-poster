<?php
namespace verbb\socialposter\controllers;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\base\AccountInterface;

use Craft;
use craft\helpers\ArrayHelper;
use craft\helpers\Json;
use craft\web\Controller;

use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AccountsController extends Controller
{
    // Public Methods
    // =========================================================================

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

        $account = $accountsService->createAccount([
            'id' => $accountId,
            'type' => $type,
            'name' => $this->request->getParam('name'),
            'handle' => $this->request->getParam('handle'),
            'enabled' => (bool)$this->request->getParam('enabled'),
            'autoPost' => (bool)$this->request->getParam('autoPost'),
            'settings' => $this->request->getParam("types.$type"),
        ]);

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
        $this->requireAcceptsJson();

        $accountId = $this->request->getRequiredBodyParam('id');

        SocialPoster::$plugin->getAccounts()->deleteAccountById($accountId);

        return $this->asSuccess();
    }

    public function actionRefreshSettings(): Response
    {
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
}
