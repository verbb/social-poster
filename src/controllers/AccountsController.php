<?php
namespace verbb\socialposter\controllers;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\events\OauthTokenEvent;
use verbb\socialposter\models\Account;
use verbb\socialposter\models\Token;

use Craft;
use craft\helpers\ArrayHelper;
use craft\helpers\Json;
use craft\web\Controller;

use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AccountsController extends Controller
{
    // Constants
    // =========================================================================

    const EVENT_AFTER_OAUTH_CALLBACK = 'afterOauthCallback';


    // Properties
    // =========================================================================

    protected $allowAnonymous = ['callback'];
    private $redirect;
    private $originUrl;


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

    public function actionSave(): Response
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $session = Craft::$app->getSession();

        $accountId = $request->getBodyParam('accountId');
        $account = SocialPoster::$plugin->getAccounts()->getAccountById($accountId);

        if (!$account) {
            $account = new Account();
        }

        $account->id = $accountId;
        $account->name = $request->getBodyParam('name');
        $account->handle = $request->getBodyParam('handle');
        $account->enabled = $request->getBodyParam('enabled');
        $account->autoPost = $request->getBodyParam('autoPost');
        $account->providerHandle = $request->getBodyParam('providerHandle');
        $account->settings = $request->getBodyParam('providerSettings.' . $account->providerHandle);

        if (SocialPoster::$plugin->getAccounts()->saveAccount($account)) {
            $session->setNotice(Craft::t('social-poster', 'Account saved successfully.'));
        } else {
            $session->setError($result);
        }

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

        $session->set('socialposter.controllerUrl', $request->getAbsoluteUrl());

        $this->originUrl = $session->get('socialposter.originUrl');

        if (!$this->originUrl) {
            $this->originUrl = $request->referrer;
            $session->set('socialposter.originUrl', $this->originUrl);
        }

        $this->redirect = $request->getParam('redirect');
        $accountId = $request->getParam('accountId');

        $account = SocialPoster::$plugin->getAccounts()->getAccountById($accountId);

        try {
            $provider = $account->provider;

            if (!$provider) {
                throw new \Exception('Provider is not configured');
            }

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

            throw new \Exception($callbackResponse['errorMsg']);
        } catch (\Throwable $e) {
            $errorMsg = $e->getMessage();

            SocialPoster::error('Couldn’t connect to ' . $account->provider . ' ' . $e->getMessage() . ' - ' . $e->getFile() . ': ' . $e->getLine() . '.');
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
                'success' => true
            ]);
        }

        Craft::$app->getSession()->setNotice(Craft::t('social-poster', 'Account disconnected.'));

        return $this->redirect($request->referrer);
    }

    public function actionCallback(): Response
    {
        Craft::$app->getSession()->set('socialposter.callback', true);

        $url = Craft::$app->getSession()->get('socialposter.controllerUrl');

        if (strpos($url, '?') === false) {
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

    private function _createToken($response, $account)
    {
        $token = new Token();
        $token->providerHandle = $account->provider->getHandle();

        switch ($account->provider->oauthVersion()) {
            case 1: {
                $token->accessToken = $response['token']->getIdentifier();
                $token->secret = $response['token']->getSecret();

                break;
            }
            case 2: {
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
            SocialPoster::error('Unable to save token - ' . json_encode($token->getErrors()) . '.');
        
            return null;
        }

        $account->tokenId = $token->id;

        if (!SocialPoster::$plugin->getAccounts()->saveAccount($account)) {
            SocialPoster::error('Unable to save account - ' . json_encode($account->getErrors()) . '.');
        
            return null;
        }

        $this->_cleanSession();

        if (!$this->redirect) {
            $this->redirect = $this->originUrl;
        }

        Craft::$app->getSession()->setNotice(Craft::t('social-poster', 'Account connected.'));

        return $this->redirect($this->redirect);
    }

    private function _deleteToken($account)
    {
        if (!SocialPoster::$plugin->getTokens()->deleteTokenById($account->tokenId)) {
            SocialPoster::error('Unable to delete token - ' . json_encode($token->getErrors()) . '.');
        
            return null;
        }

        $account->tokenId = null;

        if (!SocialPoster::$plugin->getAccounts()->saveAccount($account)) {
            SocialPoster::error('Unable to save account - ' . json_encode($account->getErrors()) . '.');
        
            return null;
        }
    }

    private function _cleanSession()
    {
        Craft::$app->getSession()->remove('socialposter.originUrl');
    }
}

