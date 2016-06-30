<?php
namespace Craft;

class SocialPoster_OauthController extends BaseController
{
    public function actionConnect()
    {
        $referer = craft()->httpSession->get('socialPoster.referer');

        if (!$referer) {
            $referer = craft()->request->getUrlReferrer();
            craft()->httpSession->add('socialPoster.referer', $referer);
            SocialPosterPlugin::log('OAuth Connect Referer: ' . $referer, LogLevel::Info);
        }

        $providerHandle = craft()->request->getParam('handle');
        $provider = craft()->socialPoster_accounts->getAccountProvider($providerHandle);

        $oauthProviderHandle = $provider['oauthProviderHandle'];
        $oauthProvider = craft()->oauth->getProvider($oauthProviderHandle);

        if ($oauthProvider) {
            $response = craft()->oauth->connect(array(
                'plugin' => 'socialPoster',
                'provider' => $oauthProviderHandle,
                'scope' => $provider->getScope(),
                'authorizationOptions' => $provider->getAuthorizationOptions(),
            ));

            if ($response) {
                if ($response['success']) {
                    if ($response['token']) {
                        SocialPosterPlugin::log('Token: ' . print_r($response['token']->attributes, true), LogLevel::Info);
                    } else {
                        SocialPosterPlugin::log('Couldn’t get token', LogLevel::Error);
                    }

                    $result = craft()->socialPoster_accounts->saveToken($providerHandle, $response['token']);
                    
                    if ($result) {
                        craft()->userSession->setNotice(Craft::t('Connected.'));
                    } else {
                        craft()->userSession->setError(Craft::t('Couldn’t OAuth connect.'));
                    }
                } else {
                    craft()->userSession->setError(Craft::t($response['errorMsg']));
                }
            } else {
                craft()->userSession->setError(Craft::t('Couldn’t OAuth connect.'));
            }
        } else {
            craft()->userSession->setError(Craft::t('OAuth provider not configured.'));
        }

        craft()->httpSession->remove('socialPoster.referer');

        $this->redirect($referer);
    }
}

