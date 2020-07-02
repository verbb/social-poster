<?php
namespace verbb\socialposter\services;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\base\Provider;
use verbb\socialposter\base\ProviderInterface;
use verbb\socialposter\events\RegisterProviderTypesEvent;
use verbb\socialposter\providers\Facebook;
use verbb\socialposter\providers\LinkedIn;
use verbb\socialposter\providers\Twitter;

use Craft;

use yii\base\Component;

class Providers extends Component
{
    // Constants
    // =========================================================================

    const EVENT_REGISTER_PROVIDER_TYPES = 'registerProviderTypes';


    // Public Methods
    // =========================================================================

    public function getAllProviders(): array
    {
        $providerTypes = $this->_getProviderTypes();

        $providers = [];

        foreach ($providerTypes as $providerType) {
            $provider = $this->_createProvider($providerType);

            $providers[$provider->getHandle()] = $provider;
        }

        ksort($providers);

        return $providers;
    }

    public function getProvider($handle)
    {
        $providers = $this->getAllProviders();

        foreach ($providers as $provider) {
            if ($provider->getHandle() == $handle) {
                return $provider;
            }
        }
    }

    public function getOauthProviderConfig($handle): array
    {
        $config = [
            'options' => $this->getOauthConfigItem($handle, 'options'),
            'scope' => $this->getOauthConfigItem($handle, 'scope'),
            'authorizationOptions' => $this->getOauthConfigItem($handle, 'authorizationOptions'),
        ];

        $provider = $this->getProvider($handle);

        if ($provider && !isset($config['options']['redirectUri'])) {
            $config['options']['redirectUri'] = $provider->getRedirectUri();
        }

        return $config;
    }

    public function getProviderConfig($handle)
    {
        $configSettings = Craft::$app->config->getConfigFromFile('social-poster');

        if (isset($configSettings['providers'][$handle])) {
            return $configSettings['providers'][$handle];
        }

        return [];
    }

    public function saveProviderSettings($handle, $providerSettings)
    {
        $settings = SocialPoster::$plugin->getSettings()->toArray();
        $storedSettings = Craft::$app->plugins->getStoredPluginInfo('social-poster')['settings'];

        $settings['providers'] = [];

        if (isset($storedSettings['providers'])) {
            $settings['providers'] = $storedSettings['providers'];
        }

        $settings['providers'][$handle] = $providerSettings;

        $plugin = Craft::$app->getPlugins()->getPlugin('social-poster');

        return Craft::$app->getPlugins()->savePluginSettings($plugin, $settings);
    }


    // Private Methods
    // =========================================================================

    private function _getProviderTypes(): array
    {
        $providerTypes = [
            Facebook::class,
            LinkedIn::class,
            Twitter::class,
        ];

        $event = new RegisterProviderTypesEvent([
            'providerTypes' => $providerTypes
        ]);

        $this->trigger(self::EVENT_REGISTER_PROVIDER_TYPES, $event);

        return $event->providerTypes;
    }

    private function _createProvider($providerType): ProviderInterface
    {
        return new $providerType;
    }

    private function getOauthConfigItem(string $providerHandle, string $key): array
    {
        $configSettings = Craft::$app->config->getConfigFromFile('social-poster');

        if (isset($configSettings['providers'][$providerHandle]['oauth'][$key])) {
            return $configSettings['providers'][$providerHandle]['oauth'][$key];
        }

        $storedSettings = Craft::$app->plugins->getStoredPluginInfo('social-poster')['settings'];

        if (isset($storedSettings['providers'][$providerHandle]['oauth'][$key])) {
            return $storedSettings['providers'][$providerHandle]['oauth'][$key];
        }

        return [];
    }

}
