<?php
namespace Craft;

class SocialPoster_PluginService extends BaseApplicationComponent
{
    // Public Methods
    // =========================================================================

    public function checkRequirements()
    {
        $dependencies = craft()->socialPoster_plugin->getPluginDependencies();

        if (count($dependencies) > 0) {
            return $dependencies;
        }
    }

    public function getPluginDependencies($missingOnly = true)
    {
        $dependencies = array();
        $plugins = craft()->plugins->getPlugin('socialPoster')->getRequiredPlugins();

        foreach ($plugins as $key => $plugin) {
            $dependency = $this->_getPluginDependency($plugin);

            if ($missingOnly) {
                if ($dependency['isMissing']) {
                    $dependencies[] = $dependency;
                }
            } else {
                $dependencies[] = $dependency;
            }
        }

        return $dependencies;
    }

    

    // Private Methods
    // =========================================================================

    private function _getPluginDependency(array $dependency)
    {
        $isDependencyMissing = true;
        $requiresUpdate = true;

        $plugin = craft()->plugins->getPlugin($dependency['handle'], false);

        if ($plugin) {
            if (version_compare($plugin->version, $dependency['version']) >= 0) {
                $requiresUpdate = false;

                if ($plugin->isInstalled && $plugin->isEnabled) {
                    $isDependencyMissing = false;
                }
            }
        }

        $dependency['isMissing'] = $isDependencyMissing;
        $dependency['requiresUpdate'] = $requiresUpdate;
        $dependency['plugin'] = $plugin;

        return $dependency;
    }

}