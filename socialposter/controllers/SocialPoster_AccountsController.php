<?php
namespace Craft;

class SocialPoster_AccountsController extends BaseController
{
    // Public Methods
    // =========================================================================

    //
    // Control Panel
    //

    public function actionIndex()
    {
        craft()->runController('socialPoster/plugin/checkRequirements');

        $providers = craft()->socialPoster_accounts->getAccountProviders(false);
        $accounts = craft()->socialPoster_accounts->getAll();

        $this->renderTemplate('socialposter/accounts', array(
            'providers' => $providers,
            'accounts' => $accounts,
        ));
    }

    public function actionEdit(array $variables = array())
    {
        craft()->runController('socialPoster/plugin/checkRequirements');
        
        $providerHandle = $variables['providerHandle'];

        $provider = craft()->socialPoster_accounts->getAccountProvider($providerHandle);
        $account = craft()->socialPoster_accounts->getByHandle($providerHandle);

        $this->renderTemplate('socialposter/accounts/_edit', array(
            'provider' => $provider,
            'account' => $account,
        ));
    }

    public function actionSave()
    {
        $this->requirePostRequest();

        $handle = craft()->request->getPost('handle');
        $model = craft()->socialPoster_accounts->getByHandle($handle);

        if (!$model) {
            $model = new SocialPoster_AccountModel();
            $model->handle = $handle;
        }

        $model->providerSettings = craft()->request->getPost('providerSettings');

        if ($result = craft()->socialPoster_accounts->save($model)) {
            craft()->userSession->setNotice(Craft::t('Account saved successfully.'));
        } else {
            craft()->userSession->setError($result);
        }

        craft()->request->redirect(craft()->request->urlReferrer);
    }



    //
    // OAuth
    //

    public function actionConnect()
    {
        $referer = craft()->httpSession->get('socialPoster.referer');

        if (!$referer) {
            $referer = craft()->request->getUrlReferrer();
            craft()->httpSession->add('socialPoster.referer', $referer);
        }

        $handle = craft()->request->getParam('handle');
        $redirectUrl = UrlHelper::getActionUrl('socialPoster/oauth/connect', array('handle' => $handle));
        $this->redirect($redirectUrl);
    }

    public function actionDisconnect()
    {
        $handle = craft()->request->getParam('handle');
        $account = craft()->socialPoster_accounts->getByHandle($handle);

        if ($account) {
            craft()->oauth->deleteToken($account->getToken());
            $account->tokenId = null;
            craft()->socialPoster_accounts->save($account);
        }

        craft()->userSession->setNotice(Craft::t("Disconnected."));
        $referrer = craft()->request->getUrlReferrer();
        $this->redirect($referrer);
    }
}

