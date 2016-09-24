<?php
namespace Craft;

class SocialPosterPlugin extends BasePlugin
{
    // =========================================================================
    // PLUGIN INFO
    // =========================================================================

    public function getName()
    {
        return Craft::t('Social Poster');
    }

    public function getVersion()
    {
        return '1.2.0';
    }

    public function getSchemaVersion()
    {
        return '1.2.0';
    }

    public function getDeveloper()
    {
        return 'S. Group';
    }

    public function getDeveloperUrl()
    {
        return 'http://sgroup.com.au';
    }

    public function getPluginUrl()
    {
        return 'https://github.com/engram-design/SocialPoster';
    }

    public function getDocumentationUrl()
    {
        return $this->getPluginUrl() . '/blob/master/README.md';
    }

    public function getReleaseFeedUrl()
    {
        return 'https://raw.githubusercontent.com/engram-design/SocialPoster/master/changelog.json';
    }

    public function getRequiredPlugins()
    {
        return array(
            array(
                'name' => 'OAuth',
                'handle' => 'oauth',
                'url' => 'https://dukt.net/craft/oauth',
                'version' => '2.0.0'
            )
        );
    }

    public function hasCpSection()
    {
        return false;
    }

    public function getSettingsUrl()
    {
        return 'socialposter';
    }

    public function registerCpRoutes()
    {
        return array(
            'socialposter' => array('action' => 'socialPoster/posts/index'),
            'socialposter/posts' => array('action' => 'socialPoster/posts/index'),
            'socialposter/posts/(?P<postId>\d+)' => array('action' => 'socialPoster/posts/edit'),
            'socialposter/accounts' => array('action' => 'socialPoster/accounts/index'),
            'socialposter/accounts/(?P<providerHandle>{handle})' => array('action' => 'socialPoster/accounts/edit'),
            'socialposter/settings' => array('action' => 'socialPoster/settings'),
        );
    }

    protected function defineSettings()
    {
        return array(
            'enabledSections' => array(AttributeType::Mixed, 'default' => '*'),
        );
    }

    public function onBeforeUninstall()
    {
        if (isset(craft()->oauth)) {
            craft()->oauth->deleteTokensByPlugin('socialPoster');
        }
    }

    public function onBeforeInstall()
    {   
        if (version_compare(craft()->getVersion(), '2.5', '<')) {
            throw new Exception($this->getName() . ' requires Craft CMS 2.5+ in order to run.');
        }
    }


    // =========================================================================
    // HOOKS
    // =========================================================================

    public function init()
    {
        // Hook to trigger sending on-save (but only for new entries)
        craft()->on('entries.saveEntry', function(Event $event) {
            craft()->socialPoster->onSaveEntry($event);
        });

        if (craft()->request->isCpRequest()) {
            craft()->socialPoster->renderEntrySidebar();
        }
    }

    
}
