<?php
namespace verbb\socialposter\base;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\helpers\SocialPosterHelper;
use verbb\socialposter\models\Account;
use verbb\socialposter\models\Token;

use Craft;
use craft\base\SavableComponent;
use craft\helpers\Json;
use craft\web\Response;

use GuzzleHttp\Exception\RequestException;

abstract class Provider extends SavableComponent implements ProviderInterface
{
    // Properties
    // =========================================================================

    public $account;


    // Public Methods
    // =========================================================================

    public function __toString()
    {
        return $this->getName();
    }

    public function getHandle(): string
    {
        $class = $this->displayName();

        return strtolower($class);
    }

    public function getIconUrl()
    {
        return Craft::$app->assetManager->getPublishedUrl('@verbb/socialposter/resources/dist/img/' . $this->getHandle() . '.svg', true);
    }

    public function isConfigured(): bool
    {
        $config = $this->getOauthProviderConfig();

        if (!empty($config['options']['clientId'])) {
            return true;
        }

        return false;
    }

    public function getManagerUrl()
    {
        return null;
    }

    public function getScopeDocsUrl()
    {
        return null;
    }

    public function getSettingsHtml()
    {
        return null;
    }

    public function getInputHtml($context)
    {
        $variables = $context;
        $variables['provider'] = $this;

        return Craft::$app->getView()->renderTemplate('social-poster/_providers/' . $this->getHandle() . '/input', $variables);
    }

    public function oauthVersion(): int
    {
        return 2;
    }

    public function oauthConnect()
    {
        switch ($this->oauthVersion()) {
            case 1: {
                return $this->oauth1Connect();
            }
            case 2: {
                return $this->oauth2Connect();
            }
        }

        throw new LoginException('OAuth version not supported');
    }

    public function oauthCallback()
    {
        switch ($this->oauthVersion()) {
            case 1: {
                return $this->oauth1Callback();
            }
            case 2: {
                return $this->oauth2Callback();
            }
        }

        throw new LoginException('OAuth version not supported');
    }

    public function getOauthScope(): array
    {
        $scope = $this->getDefaultOauthScope();
        $oauthProviderConfig = $this->getOauthProviderConfig();

        if (isset($oauthProviderConfig['scope'])) {
            $scope = $oauthProviderConfig['scope'];
        }

        return $scope;
    }

    public function getOauthAuthorizationOptions()
    {
        $authorizationOptions = $this->getDefaultOauthAuthorizationOptions();
        $config = $this->getOauthProviderConfig();

        if (isset($config['authorizationOptions'])) {
            $authorizationOptions = array_merge($authorizationOptions, $config['authorizationOptions']);
        }

        return $authorizationOptions;
    }

    public function getRedirectUri(): string
    {
        return SocialPosterHelper::siteActionUrl('social-poster/accounts/callback');
    }


    // Protected Methods
    // =========================================================================

    protected function getDefaultOauthAuthorizationOptions(): array
    {
        return [];
    }

    protected function getDefaultOauthScope(): array
    {
        return [];
    }

    protected function getOauthProviderConfig(): array
    {
        return SocialPoster::$plugin->getProviders()->getOauthProviderConfig($this->getHandle());
    }

    protected function getProviderConfig(): array
    {
        return SocialPoster::$plugin->getProviders()->getProviderConfig($this->getHandle());
    }

    protected function getPostExceptionResponse($exception)
    {
        $statusCode = '[error]';
        $data = [];
        $reasonPhrase = $exception->getMessage();

        // Check for Guzzle errors, which are truncated in the exception `getMessage()`.
        if ($exception instanceof RequestException && $exception->getResponse()) {
            $response = $exception->getResponse();
            $statusCode = $response->getStatusCode();
            $reasonPhrase = $response->getReasonPhrase();

            $data = Json::decode((string)$response->getBody()->getContents());
        }

        // Save more detail to the log file
        SocialPoster::error(Craft::t('social-poster', 'Error posting to {provider}: “{message}” {file}:{line}', [
            'provider' => $this->getName(),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]));

        return [
            'success' => false,
            'data' => $data,
            'response' => [
                'statusCode' => $statusCode,
                'reasonPhrase' => $reasonPhrase,
            ]
        ];
    }

    protected function getPostResponse($response)
    {
        $data = Json::decode((string)$response->getBody());

        $responseReturn = [
            'statusCode' => $response->getStatusCode(),
            'reasonPhrase' => $response->getReasonPhrase(),
        ];

        $success = isset($data['id']) ? true : false;

        return [
            'success' => $success,
            'data' => $data,
            'response' => $responseReturn,
        ];
    }


    // Private Methods
    // =========================================================================

    private function oauth1Connect(): Response
    {
        // OAuth provider
        $provider = $this->getOauthProvider();

        // Obtain temporary credentials
        $temporaryCredentials = $provider->getTemporaryCredentials();

        // Store credentials in the session
        Craft::$app->getSession()->set('oauth.temporaryCredentials', $temporaryCredentials);

        // Redirect to login screen
        $authorizationUrl = $provider->getAuthorizationUrl($temporaryCredentials);

        return Craft::$app->getResponse()->redirect($authorizationUrl);
    }

    private function oauth2Connect(): Response
    {
        $provider = $this->getOauthProvider();

        Craft::$app->getSession()->set('socialposter.oauthState', $provider->getState());

        $scope = $this->getOauthScope();
        $options = $this->getOauthAuthorizationOptions();

        if (!is_array($options)) {
            $options = [];
        }

        $options['scope'] = $scope;

        $authorizationUrl = $provider->getAuthorizationUrl($options);

        return Craft::$app->getResponse()->redirect($authorizationUrl);
    }

    private function oauth1Callback(): array
    {
        $provider = $this->getOauthProvider();

        $oauthToken = Craft::$app->getRequest()->getParam('oauth_token');
        $oauthVerifier = Craft::$app->getRequest()->getParam('oauth_verifier');

        // Retrieve the temporary credentials we saved before.
        $temporaryCredentials = Craft::$app->getSession()->get('oauth.temporaryCredentials');

        // Obtain token credentials from the server.
        $token = $provider->getTokenCredentials($temporaryCredentials, $oauthToken, $oauthVerifier);

        return [
            'success' => true,
            'token' => $token
        ];
    }

    private function oauth2Callback(): array
    {
        $provider = $this->getOauthProvider();

        $code = Craft::$app->getRequest()->getParam('code');

        // Try to get an access token (using the authorization code grant)
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $code
        ]);

        return [
            'success' => true,
            'token' => $token
        ];
    }

    private function mergeArrayValues(array $array, array $array2): array
    {
        foreach ($array2 as $value2) {
            $addValue = true;

            foreach ($array as $value) {
                if ($value === $value2) {
                    $addValue = false;
                }
            }

            if ($addValue) {
                $array[] = $value2;
            }
        }

        return $array;
    }
}
