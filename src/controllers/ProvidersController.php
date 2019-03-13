<?php
namespace verbb\socialposter\controllers;

use verbb\socialposter\SocialPoster;

use Craft;
use craft\web\Controller;

use yii\web\HttpException;
use yii\web\Response;

class ProvidersController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionIndex(): Response
    {   
        $providers = SocialPoster::$plugin->getProviders()->getAllProviders();

        return $this->renderTemplate('social-poster/providers', [
            'providers' => $providers,
        ]);
    }

    public function actionOauth($handle): Response
    {
        $provider = SocialPoster::$plugin->getProviders()->getProvider($handle, false);
        $oauthProviderConfig = SocialPoster::$plugin->getProviders()->getOauthProviderConfig($handle);

        if ($provider) {
            return $this->renderTemplate('social-poster/providers/_oauth', [
                'provider' => $provider,
                'oauthProviderConfig' => $oauthProviderConfig,
            ]);
        }

        throw new HttpException(404);
    }

    public function actionSaveOauthProvider()
    {
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();

        $handle = $request->getBodyParam('handle');

        $settings = [
            'oauth' => [
                'options' => [
                    'clientId' => $request->getBodyParam('clientId'),
                    'clientSecret' => $request->getBodyParam('clientSecret'),
                ]
            ]
        ];

        if (SocialPoster::$plugin->getProviders()->saveProviderSettings($handle, $settings)) {
            Craft::$app->getSession()->setNotice(Craft::t('social-poster', 'Provider saved.'));

            return $this->redirectToPostedUrl();
        }

        Craft::$app->getSession()->setError(Craft::t('social-poster', 'Couldn’t save provider.'));

        return null;
    }

}