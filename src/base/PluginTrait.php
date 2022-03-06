<?php
namespace verbb\socialposter\base;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\services\Accounts;
use verbb\socialposter\services\Posts;
use verbb\socialposter\services\Providers;
use verbb\socialposter\services\Service;
use verbb\socialposter\services\Tokens;

use Craft;

use yii\log\Logger;

use verbb\base\BaseHelper;

trait PluginTrait
{
    // Properties
    // =========================================================================

    public static SocialPoster $plugin;


    // Static Methods
    // =========================================================================

    public static function log($message): void
    {
        Craft::getLogger()->log($message, Logger::LEVEL_INFO, 'social-poster');
    }

    public static function error($message): void
    {
        Craft::getLogger()->log($message, Logger::LEVEL_ERROR, 'social-poster');
    }


    // Public Methods
    // =========================================================================

    public function getAccounts(): Accounts
    {
        return $this->get('accounts');
    }

    public function getPosts(): Posts
    {
        return $this->get('posts');
    }

    public function getProviders(): Providers
    {
        return $this->get('providers');
    }

    public function getService(): Service
    {
        return $this->get('service');
    }

    public function getTokens(): Tokens
    {
        return $this->get('tokens');
    }


    // Private Methods
    // =========================================================================

    private function _setPluginComponents(): void
    {
        $this->setComponents([
            'accounts' => Accounts::class,
            'posts' => Posts::class,
            'providers' => Providers::class,
            'service' => Service::class,
            'tokens' => Tokens::class,
        ]);

        BaseHelper::registerModule();
    }

    private function _setLogging(): void
    {
        BaseHelper::setFileLogging('social-poster');
    }

}