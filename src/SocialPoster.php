<?php
namespace verbb\socialposter;

use verbb\socialposter\base\PluginTrait;
use verbb\socialposter\elements\Post;
use verbb\socialposter\models\Settings;
use verbb\socialposter\variables\SocialPosterVariable;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\console\Application as ConsoleApplication;
use craft\console\Controller as ConsoleController;
use craft\console\controllers\ResaveController;
use craft\elements\Entry;
use craft\events\DefineConsoleActionsEvent;
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
    public string $schemaVersion = '2.0.2';
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

        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->_registerResaveCommand();
        }

        if (Craft::$app->getRequest()->getIsSiteRequest()) {
            $this->_registerSiteRoutes();
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
        $nav = parent::getCpNavItem();

        $nav['label'] = $this->getPluginName();

        if (Craft::$app->getUser()->checkPermission('socialPoster-posts')) {
            $nav['subnav']['posts'] = [
                'label' => Craft::t('social-poster', 'Posts'),
                'url' => 'social-poster/posts',
            ];
        }

        if (Craft::$app->getUser()->checkPermission('socialPoster-accounts')) {
            $nav['subnav']['accounts'] = [
                'label' => Craft::t('social-poster', 'Accounts'),
                'url' => 'social-poster/accounts',
            ];
        }

        if (Craft::$app->getUser()->getIsAdmin() && Craft::$app->getConfig()->getGeneral()->allowAdminChanges) {
            $nav['subnav']['settings'] = [
                'label' => Craft::t('social-poster', 'Settings'),
                'url' => 'social-poster/settings',
            ];
        }

        return $nav;
    }


    // Protected Methods
    // =========================================================================

    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }


    // Private Methods
    // =========================================================================

    private function _registerCpRoutes(): void
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules['social-poster'] = 'social-poster/posts/index';
            $event->rules['social-poster/posts'] = 'social-poster/posts/index';
            $event->rules['social-poster/posts/<postId:\d+>'] = 'social-poster/posts/edit';
            $event->rules['social-poster/accounts'] = 'social-poster/accounts/index';
            $event->rules['social-poster/accounts/new'] = 'social-poster/accounts/edit';
            $event->rules['social-poster/accounts/<handle:{handle}>'] = 'social-poster/accounts/edit';
            $event->rules['social-poster/settings'] = 'social-poster/plugin/settings';
        });
    }

    private function _registerSiteRoutes(): void
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_SITE_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules['social-poster/auth/callback'] = 'social-poster/auth/callback';
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
                    'socialPoster-settings' => ['label' => Craft::t('social-poster', 'Settings')],
                ],
            ];
        });
    }

    private function _registerResaveCommand(): void
    {
        if (!Craft::$app instanceof ConsoleApplication) {
            return;
        }

        Event::on(ResaveController::class, ConsoleController::EVENT_DEFINE_ACTIONS, function(DefineConsoleActionsEvent $event) {
            $event->actions['socialposter-posts'] = [
                'action' => function(): int {
                    return Craft::$app->controller->resaveElements(Post::class);
                },
                'options' => [],
                'helpSummary' => 'Re-saves Social Poster posts.',
            ];
        });
    }
}
