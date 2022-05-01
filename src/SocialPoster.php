<?php
namespace verbb\socialposter;

use verbb\socialposter\base\PluginTrait;
use verbb\socialposter\elements\Post;
use verbb\socialposter\models\Settings;
use verbb\socialposter\variables\SocialPosterVariable;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\elements\Entry;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterUserPermissionsEvent;
use craft\helpers\UrlHelper;
use craft\services\Elements;
use craft\services\UserPermissions;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;

class SocialPoster extends Plugin
{
    // Properties
    // =========================================================================

    public bool $hasCpSettings = true;
    public string $schemaVersion = '2.0.0';
    public string $minVersionRequired = '2.3.2';


    // Traits
    // =========================================================================

    use PluginTrait;


    // Public Methods
    // =========================================================================

    public function init(): void
    {
        parent::init();

        self::$plugin = $this;

        $this->_registerComponents();
        $this->_registerLogTarget();
        $this->_registerVariables();
        $this->_registerCraftEventListeners();
        $this->_registerElementTypes();

        if (Craft::$app->getRequest()->getIsCpRequest()) {
            $this->_registerCpRoutes();
        }
        
        if (Craft::$app->getEdition() === Craft::Pro) {
            $this->_registerPermissions();
        }

        $this->hasCpSection = $this->getSettings()->hasCpSection;
    }

    public function getPluginName(): string
    {
        return Craft::t('social-poster', $this->getSettings()->pluginName);
    }

    public function getSettingsResponse(): mixed
    {
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('social-poster/settings'));
    }

    public function getCpNavItem(): ?array
    {
        $subNavs = [];
        $navItem = parent::getCpNavItem();
        $currentUser = Craft::$app->getUser()->getIdentity();

        // Only show sub-navs the user has permission to view
        if ($currentUser->can('socialPoster-posts')) {
            $subNavs['posts'] = [
                'label' => Craft::t('social-poster', 'Posts'),
                'url' => 'social-poster/posts',
            ];
        }

        if ($currentUser->can('socialPoster-accounts')) {
            $subNavs['accounts'] = [
                'label' => Craft::t('social-poster', 'Accounts'),
                'url' => 'social-poster/accounts',
            ];
        }

        if ($currentUser->can('socialPoster-providers')) {
            $subNavs['providers'] = [
                'label' => Craft::t('social-poster', 'Providers'),
                'url' => 'social-poster/providers',
            ];
        }

        if ($currentUser->can('socialPoster-settings')) {
            $subNavs['settings'] = [
                'label' => Craft::t('social-poster', 'Settings'),
                'url' => 'social-poster/settings',
            ];
        }

        $navItem = array_merge($navItem, [
            'subnav' => $subNavs,
        ]);

        $navItem['label'] = $this->getPluginName();

        return $navItem;
    }


    // Protected Methods
    // =========================================================================

    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }


    // Private Methods
    // =========================================================================

    private function _registerCpRoutes(): void
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules = array_merge($event->rules, [
                'social-poster' => 'social-poster/posts/index',
                'social-poster/posts' => 'social-poster/posts/index',
                'social-poster/posts/<postId:\d+>' => 'social-poster/posts/edit',
                'social-poster/accounts' => 'social-poster/accounts/index',
                'social-poster/accounts/new' => 'social-poster/accounts/edit',
                'social-poster/accounts/<accountId:\d+>' => 'social-poster/accounts/edit',
                'social-poster/providers' => 'social-poster/providers/index',
                'social-poster/providers/<handle:{handle}>' => 'social-poster/providers/oauth',
                'social-poster/settings' => 'social-poster/plugin/settings',
            ]);
        });
    }

    private function _registerVariables(): void
    {
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            $event->sender->set('socialPoster', SocialPosterVariable::class);
        });
    }

    private function _registerCraftEventListeners(): void
    {
        if (Craft::$app->getRequest()->getIsCpRequest() || Craft::$app->getRequest()->getIsSiteRequest()) {
            Event::on(Entry::class, Entry::EVENT_AFTER_SAVE, [$this->getService(), 'onAfterSaveEntry']);
        }

        Event::on(Entry::class, Entry::EVENT_DEFINE_SIDEBAR_HTML, [$this->getService(), 'renderEntrySidebar']);
    }

    private function _registerElementTypes(): void
    {
        Event::on(Elements::class, Elements::EVENT_REGISTER_ELEMENT_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = Post::class;
        });
    }

    private function _registerPermissions(): void
    {
        Event::on(UserPermissions::class, UserPermissions::EVENT_REGISTER_PERMISSIONS, function(RegisterUserPermissionsEvent $event) {
            $event->permissions[] = [
                'heading' => Craft::t('social-poster', 'Social Poster'),
                'permissions' => [
                    'socialPoster-posts' => ['label' => Craft::t('social-poster', 'Posts')],
                    'socialPoster-accounts' => ['label' => Craft::t('social-poster', 'Accounts')],
                    'socialPoster-providers' => ['label' => Craft::t('social-poster', 'Providers')],
                    'socialPoster-settings' => ['label' => Craft::t('social-poster', 'Settings')],
                ],
            ];
        });
    }
}
