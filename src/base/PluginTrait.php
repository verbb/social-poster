<?php
namespace verbb\socialposter\base;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\services\Accounts;
use verbb\socialposter\services\Posts;
use verbb\socialposter\services\Service;

use verbb\base\LogTrait;
use verbb\base\helpers\Plugin;

use verbb\auth\Auth;

trait PluginTrait
{
    // Properties
    // =========================================================================

    public static ?SocialPoster $plugin = null;


    // Traits
    // =========================================================================

    use LogTrait;


    // Static Methods
    // =========================================================================

    public static function config(): array
    {
        Auth::registerModule();
        Plugin::bootstrapPlugin('social-poster');

        return [
            'components' => [
                'accounts' => Accounts::class,
                'posts' => Posts::class,
                'service' => Service::class,
            ],
        ];
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

}