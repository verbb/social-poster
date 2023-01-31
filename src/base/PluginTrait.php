<?php
namespace verbb\socialposter\base;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\services\Accounts;
use verbb\socialposter\services\Posts;
use verbb\socialposter\services\Service;

use Craft;

use yii\log\Logger;

use verbb\auth\Auth;
use verbb\base\BaseHelper;

trait PluginTrait
{
    // Properties
    // =========================================================================

    public static SocialPoster $plugin;


    // Static Methods
    // =========================================================================

    public static function log(string $message, array $params = []): void
    {
        $message = Craft::t('social-poster', $message, $params);

        Craft::getLogger()->log($message, Logger::LEVEL_INFO, 'social-poster');
    }

    public static function error(string $message, array $params = []): void
    {
        $message = Craft::t('social-poster', $message, $params);

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

    public function getService(): Service
    {
        return $this->get('service');
    }


    // Private Methods
    // =========================================================================

    private function _registerComponents(): void
    {
        $this->setComponents([
            'accounts' => Accounts::class,
            'posts' => Posts::class,
            'service' => Service::class,
        ]);

        Auth::registerModule();
        BaseHelper::registerModule();
    }

    private function _registerLogTarget(): void
    {
        BaseHelper::setFileLogging('social-poster');
    }

}