<?php
namespace verbb\socialposter\controllers;

use verbb\socialposter\SocialPoster;

use Craft;
use craft\web\Controller;

use yii\web\Response;

use verbb\auth\Auth;
use verbb\auth\helpers\Session;

use Throwable;

class AuthController extends Controller
{
    // Properties
    // =========================================================================

    protected array|int|bool $allowAnonymous = ['connect', 'callback'];


    // Public Methods
    // =========================================================================

    public function beforeAction($action): bool
    {
        // Don't require CSRF validation for callback requests
        if ($action->id === 'callback') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    public function actionConnect(): ?Response
    {
        $accountHandle = $this->request->getRequiredParam('account');

        try {
            if (!($account = SocialPoster::$plugin->getAccounts()->getAccountByHandle($accountHandle))) {
                return $this->asFailure(Craft::t('social-poster', 'Unable to find account “{account}”.', ['account' => $accountHandle]));
            }

            // Keep track of which account instance is for, so we can fetch it in the callback
            Session::set('accountHandle', $accountHandle);

            return Auth::getInstance()->getOAuth()->connect('social-poster', $account);
        } catch (Throwable $e) {
            SocialPoster::error('Unable to authorize connect “{account}”: “{message}” {file}:{line}', [
                'account' => $accountHandle,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->asFailure(Craft::t('social-poster', 'Unable to authorize connect “{account}”.', ['account' => $accountHandle]));
        }
    }

    public function actionCallback(): ?Response
    {
        // Get both the origin (failure) and redirect (success) URLs
        $origin = Session::get('origin');
        $redirect = Session::get('redirect');

        // Get the account we're current authorizing
        if (!($accountHandle = Session::get('accountHandle'))) {
            Session::setError('social-poster', Craft::t('social-poster', 'Unable to find account.'), true);

            return $this->redirect($origin);
        }

        if (!($account = SocialPoster::$plugin->getAccounts()->getAccountByHandle($accountHandle))) {
            Session::setError('social-poster', Craft::t('social-poster', 'Unable to find account “{account}”.', ['account' => $accountHandle]), true);

            return $this->redirect($origin);
        }

        try {
            // Fetch the access token from the account and create a Token for us to use
            $token = Auth::getInstance()->getOAuth()->callback('social-poster', $account);

            if (!$token) {
                Session::setError('social-poster', Craft::t('social-poster', 'Unable to fetch token.'), true);

                return $this->redirect($origin);
            }

            // Save the token to the Auth plugin, with a reference to this account
            $token->reference = $account->id;
            Auth::getInstance()->getTokens()->upsertToken($token);
        } catch (Throwable $e) {
            $error = Craft::t('social-poster', 'Unable to process callback for “{account}”: “{message}” {file}:{line}', [
                'account' => $accountHandle,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            SocialPoster::error($error);

            // Show the error detail in the CP
            Craft::$app->getSession()->setFlash('social-poster:callback-error', $error);

            return $this->redirect($origin);
        }

        Session::setNotice('social-poster', Craft::t('social-poster', '{provider} connected.', ['provider' => $account->providerName]), true);

        return $this->redirect($redirect);
    }

    public function actionDisconnect(): ?Response
    {
        $accountHandle = $this->request->getRequiredParam('account');

        if (!($account = SocialPoster::$plugin->getAccounts()->getAccountByHandle($accountHandle))) {
            return $this->asFailure(Craft::t('social-poster', 'Unable to find account “{account}”.', ['account' => $accountHandle]));
        }

        // Delete all tokens for this account
        Auth::getInstance()->getTokens()->deleteTokenByOwnerReference('social-poster', $account->id);

        return $this->asModelSuccess($account, Craft::t('social-poster', '{provider} disconnected.', ['provider' => $account->providerName]), 'account');
    }

}
