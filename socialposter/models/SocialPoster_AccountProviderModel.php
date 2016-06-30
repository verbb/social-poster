<?php
namespace Craft;

class SocialPoster_AccountProviderModel extends BaseModel
{
    // Public Methods
    // =========================================================================

    public function getName()
    {
        return $this->getOauthProvider()->getName();
    }

    public function getIconUrl()
    {
        return $this->getOauthProvider()->getIconUrl();
    }

    public function getHandle()
    {
        return $this->getOauthProvider()->getHandle();
    }

    public function getOauthProvider()
    {
        return craft()->oauth->getProvider($this->oauthProviderHandle, false);
    }

    public function getScope()
    {
        return craft()->config->get($this->getHandle() . 'Scope', 'socialPoster');
    }

    public function getAuthorizationOptions()
    {
        return craft()->config->get($this->getHandle() . 'AuthorizationOptions', 'socialPoster');
    }


    // Protected Methods
    // =========================================================================

    protected function defineAttributes()
    {
        return array(
            'oauthProviderHandle' => AttributeType::String,
        );
    }

}