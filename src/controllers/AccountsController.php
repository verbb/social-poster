<?php
namespace verbb\socialposter\controllers;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\events\OauthTokenEvent;
use verbb\socialposter\models\Account;
use verbb\socialposter\models\Token;

use Craft;
use craft\helpers\ArrayHelper;
use craft\helpers\Json;
use craft\helpers\UrlHelper;
use craft\web\Controller;

use yii\web\NotFoundHttpException;
use yii\web\Response;

use Throwable;
use Exception;

class AccountsController extends Controller
{
    // Constants
    // =========================================================================

    public const EVENT_AFTER_OAUTH_CALLBACK = 'afterOauthCallback';


    // Properties
    // =========================================================================

    protected array|bool|int $allowAnonymous = ['callback'];
    
    private ?string $redirect = null;
    private ?string $originUrl = null;


    // Public Methods
    // =========================================================================

    public function actionIndex(): Response
    {
        $accounts = SocialPoster::$plugin->getAccounts()->getAllAccounts();

        return $this->renderTemplate('social-poster/accounts', [
            'accounts' => $accounts,
        ]);
    }

    public function actionEdit(int $accountId = null, Account $account = null): Response
    {
        if ($account === null) {
            if ($accountId !== null) {
                $account = SocialPoster::$plugin->getAccounts()->getAccountById($accountId);

                if (!$account) {
                    throw new NotFoundHttpException('Account not found');
                }
            } else {
                $account = new Account();
            }
        }

        $providers = SocialPoster::$plugin->getProviders()->getAllProviders();

        $providerTypeOptions = [];

        foreach ($providers as $provider) {
            $provider->account = $account;

            $providerTypeOptions[] = [
                'value' => $provider->handle,
                'label' => $provider->name,
            ];
        }

        // Sort them by name
        ArrayHelper::multisort($providerTypeOptions, 'label');

        if ($account->id) {
            $title = trim($account->name) ?: Craft::t('social-poster', 'Edit Account');
        } else {
            $title = Craft::t('social-poster', 'Create a new account');
        }

        if ($account->id) {
            $continueEditingUrl = 'social-poster/accounts/' . $account->id;
        } else {
            $continueEditingUrl = 'social-poster/accounts';
        }

        return $this->renderTemplate('social-poster/accounts/_edit', [
            'accountId' => $accountId,
            'account' => $account,
            'providers' => $providers,
            'providerTypeOptions' => $providerTypeOptions,
            'title' => $title,
            'continueEditingUrl' => $continueEditingUrl,
        ]);
    }

    public function actionSave(): ?Response
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $session = Craft::$app->getSession();

        $accountId = $request->getBodyParam('accountId');
        $account = new Account();

        if ($accountId) {
            $account = SocialPoster::$plugin->getAccounts()->getAccountById($accountId);

            if (!$account) {
                $account = new Account();
            }
        }

        $account->id = $accountId;
        $account->name = $request->getBodyParam('name');
        $account->handle = $request->getBodyParam('handle');
        $account->enabled = (bool)$request->getBodyParam('enabled');
        $account->autoPost = $request->getBodyParam('autoPost');
        $account->providerHandle = $request->getBodyParam('providerHandle');
        $account->settings = $request->getBodyParam('providerSettings.' . $account->providerHandle);

        if (!SocialPoster::$plugin->getAccounts()->saveAccount($account)) {
            $session->setError(Craft::t('social-poster', 'Unable to save account.'));

            Craft::$app->getUrlManager()->setRouteParams([
                'account' => $account,
            ]);

            return null;
        }

        $session->setNotice(Craft::t('social-poster', 'Account saved successfully.'));

        return $this->redirectToPostedUrl();
    }

    public function actionReorder(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $accountIds = Json::decode(Craft::$app->getRequest()->getRequiredBodyParam('ids'));
        SocialPoster::$plugin->getAccounts()->reorderAccounts($accountIds);

        return $this->asJson(['success' => true]);
    }

    public function actionDelete(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $accountId = Craft::$app->getRequest()->getRequiredBodyParam('id');

        SocialPoster::$plugin->getAccounts()->deleteAccountById($accountId);

        return $this->asJson(['success' => true]);
    }


    // OAuth Methods
    // =========================================================================

    public function actionConnect(): Response
    {
        $request = Craft::$app->getRequest();
        $session = Craft::$app->getSession();

        $accountId = $request->getParam('accountId');
        $controllerUrl = UrlHelper::actionUrl('social-poster/accounts/connect', ['accountId' => $accountId]);

        $session->set('socialposter.controllerUrl', $controllerUrl);

        $this->originUrl = $session->get('socialposter.originUrl');

        if (!$this->originUrl) {
            $this->originUrl = $request->referrer;
            $session->set('socialposter.originUrl', $this->originUrl);
        }

        $this->redirect = (string)$request->getParam('redirect');

        if (!$accountId) {
            throw new Exception('Account ID `' . $accountId . '` missing.');
        }

        $account = SocialPoster::$plugin->getAccounts()->getAccountById($accountId);

        if (!$account) {
            throw new Exception('Account #' . $accountId . ' does not exist.');
        }

        try {
            $provider = $account->getProvider();

            if (!$provider) {
                throw new Exception('Provider is not configured');
            }

            // Set the account on the provider so we can access stuff
            $provider->account = $account;

            // Redirect to provider’s authorization page
            $session->set('socialposter.provider', $account->providerHandle);

            if (!$session->get('socialposter.callback')) {
                return $provider->oauthConnect();
            }

            // Callback
            $session->remove('socialposter.callback');

            $callbackResponse = $provider->oauthCallback();

            if ($callbackResponse['success']) {
                return $this->_createToken($callbackResponse, $account);
            }

            throw new Exception($callbackResponse['errorMsg']);
        } catch (Throwable $e) {
            $errorMsg = $e->getMessage();

            // Try and get a more meaningful error message
            $errorTitle = $request->getParam('error');
            $errorDescription = $request->getParam('error_description');

            SocialPoster::error('Couldn’t connect to ' . $account->getProvider() . ' ' . $e->getMessage() . ' - ' . $e->getFile() . ': ' . $e->getLine() . '.');

            if ($errorTitle || $errorDescription) {
                $errorMsg = $errorTitle . ' ' . $errorDescription;
            }

            SocialPoster::error($account->getProvider() . ' Response: ' . $errorMsg);
            $session->setFlash('error', $errorMsg);

            $this->_cleanSession();

            return $this->redirect($this->originUrl);
        }
    }

    public function actionDisconnect(): Response
    {
        $request = Craft::$app->getRequest();
        $accountId = $request->getParam('accountId');

        $account = SocialPoster::$plugin->getAccounts()->getAccountById($accountId);

        $this->_deleteToken($account);

        if ($request->getAcceptsJson()) {
            return $this->asJson([
                'success' => true,
            ]);
        }

        Craft::$app->getSession()->setNotice(Craft::t('social-poster', 'Account disconnected.'));

        return $this->redirect($request->referrer);
    }

    public function actionCallback(): Response
    {
        Craft::$app->getSession()->set('socialposter.callback', true);

        $url = Craft::$app->getSession()->get('socialposter.controllerUrl');

        if (!str_contains($url, '?')) {
            $url .= '?';
        } else {
            $url .= '&';
        }

        $queryParams = Craft::$app->getRequest()->getQueryParams();

        if (isset($queryParams['p'])) {
            unset($queryParams['p']);
        }

        $url .= http_build_query($queryParams);

        return $this->redirect($url);
    }


    // Private Methods
    // =========================================================================

    private function _createToken($response, $account): ?Response
    {
        $token = new Token();
        $token->providerHandle = $account->provider->getHandle();

        switch ($account->provider->oauthVersion()) {
            case 1:
            {
                $token->accessToken = $response['token']->getIdentifier();
                $token->secret = $response['token']->getSecret();

                break;
            }
            case 2:
            {
                $token->accessToken = $response['token']->getToken();
                $token->endOfLife = $response['token']->getExpires();
                $token->refreshToken = $response['token']->getRefreshToken();

                break;
            }
        }

        // Fire a 'afterOauthCallback' event
        if ($this->hasEventHandlers(self::EVENT_AFTER_OAUTH_CALLBACK)) {
            $this->trigger(self::EVENT_AFTER_OAUTH_CALLBACK, new OauthTokenEvent([
                'token' => $token,
            ]));
        }

        if (!SocialPoster::$plugin->getTokens()->saveToken($token)) {
            SocialPoster::error('Unable to save token - ' . Json::encode($token->getErrors()) . '.');

            return null;
        }

        $account->tokenId = $token->id;

        if (!SocialPoster::$plugin->getAccounts()->saveAccount($account)) {
            SocialPoster::error('Unable to save account - ' . Json::encode($account->getErrors()) . '.');

            return null;
        }

        $this->_cleanSession();

        if (!$this->redirect) {
            $this->redirect = $this->originUrl;
        }

        Craft::$app->getSession()->setNotice(Craft::t('social-poster', 'Account connected.'));

        return $this->redirect($this->redirect);
    }

    private function _deleteToken($account): void
    {
        if (!SocialPoster::$plugin->getTokens()->deleteTokenById($account->tokenId)) {
            SocialPoster::error('Unable to delete token ' . $account->tokenId . '.');
        }

        $account->tokenId = null;

        if (!SocialPoster::$plugin->getAccounts()->saveAccount($account)) {
            SocialPoster::error('Unable to save account - ' . Json::encode($account->getErrors()) . '.');
        }
    }

    private function _cleanSession(): void
    {
        Craft::$app->getSession()->remove('socialposter.originUrl');
    }
}

