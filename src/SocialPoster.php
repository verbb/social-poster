<?php
namespace verbb\socialposter;

use verbb\socialposter\base\PluginTrait;
use verbb\socialposter\elements\Account;
use verbb\socialposter\models\Settings;
use verbb\socialposter\variables\SocialPosterVariable;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\UrlHelper;
use craft\services\Elements;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;
use yii\web\User;

class SocialPoster extends Plugin
{
    // Public Properties
    // =========================================================================

    public $schemaVersion = '2.0.0';
    public $hasCpSettings = true;


    // Traits
    // =========================================================================

    use PluginTrait;


    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();

        self::$plugin = $this;

        $this->_setPluginComponents();
        $this->_setLogging();
        $this->_registerCpRoutes();
        $this->_registerVariables();
        $this->_registerCraftEventListeners();
        $this->_registerElementTypes();

        Craft::$app->view->hook('cp.entries.edit.details', [$this->getPosts(), 'renderEntrySidebar']);

        $this->hasCpSection = $this->getSettings()->hasCpSection;
    }

    public function getPluginName()
    {
        return Craft::t('social-poster', $this->getSettings()->pluginName);
    }

    public function getSettingsResponse()
    {
        Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('social-poster/settings'));
    }

    public function getCpNavItem(): array
    {
        $navItems = parent::getCpNavItem();

        $navItem['label'] = $this->getPluginName();

        $navItems['subnav']['posts'] = [
            'label' => Craft::t('social-poster', 'Posts'),
            'url' => 'social-poster/posts',
        ];

        $navItems['subnav']['accounts'] = [
            'label' => Craft::t('social-poster', 'Accounts'),
            'url' => 'social-poster/accounts',
        ];

        $navItems['subnav']['providers'] = [
            'label' => Craft::t('social-poster', 'Providers'),
            'url' => 'social-poster/providers',
        ];

        $navItems['subnav']['settings'] = [
            'label' => Craft::t('social-poster', 'Settings'),
            'url' => 'social-poster/settings',
        ];

        return $navItems;
    }


    // Protected Methods
    // =========================================================================

    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }


    // Private Methods
    // =========================================================================

    private function _registerCpRoutes()
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

    private function _registerVariables()
    {
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            $event->sender->set('socialPoster', SocialPosterVariable::class);
        });
    }

    private function _registerCraftEventListeners()
    {
        // Event::on(Elements::class, Elements::EVENT_BEFORE_SAVE_ELEMENT, [$this->getPosts(), 'onSaveElement']);
    }

    private function _registerElementTypes()
    {
        Event::on(Elements::class, Elements::EVENT_REGISTER_ELEMENT_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = Account::class;
        });
    }


    // =========================================================================
    // HOOKS
    // =========================================================================

    // public function init()
    // {
    //     // Hook to trigger sending on-save (but only for new entries)
    //     craft()->on('entries.saveEntry', function(Event $event) {
    //         craft()->socialPoster->onSaveEntry($event);
    //     });

    //     if (craft()->request->isCpRequest()) {
    //         craft()->socialPoster->renderEntrySidebar();
    //     }
    // }

    
}
