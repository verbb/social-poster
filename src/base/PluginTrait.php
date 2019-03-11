<?php
namespace verbb\socialposter\base;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\services\Accounts;
use verbb\socialposter\services\Posts;
use verbb\socialposter\services\Providers;
use verbb\socialposter\services\Service;
use verbb\socialposter\services\Tokens;

use Craft;
use craft\log\FileTarget;

use yii\log\Logger;

trait PluginTrait
{
    // Static Properties
    // =========================================================================

    public static $plugin;


    // Public Methods
    // =========================================================================

    public function getAccounts()
    {
        return $this->get('accounts');
    }

    public function getPosts()
    {
        return $this->get('posts');
    }

    public function getProviders()
    {
        return $this->get('providers');
    }

    public function getService()
    {
        return $this->get('service');
    }

    public function getTokens()
    {
        return $this->get('tokens');
    }

    private function _setPluginComponents()
    {
        $this->setComponents([
            'accounts' => Accounts::class,
            'posts' => Posts::class,
            'providers' => Providers::class,
            'service' => Service::class,
            'tokens' => Tokens::class,
        ]);
    }

    private function _setLogging()
    {
        Craft::getLogger()->dispatcher->targets[] = new FileTarget([
            'logFile' => Craft::getAlias('@storage/logs/social-poster.log'),
            'categories' => ['social-poster'],
        ]);
    }

    public static function log($message)
    {
        Craft::getLogger()->log($message, Logger::LEVEL_INFO, 'social-poster');
    }

    public static function error($message)
    {
        Craft::getLogger()->log($message, Logger::LEVEL_ERROR, 'social-poster');
    }

}