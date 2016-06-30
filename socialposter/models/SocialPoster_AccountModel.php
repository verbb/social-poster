<?php
namespace Craft;

class SocialPoster_AccountModel extends BaseModel
{
    // Public Methods
    // =========================================================================

    public function getOauthProvider()
    {
        return craft()->oauth->getProvider($this->handle);
    }

    public function getToken()
    {
        return craft()->oauth->getTokenById($this->tokenId);
    }


    // Protected Methods
    // =========================================================================

    protected function defineAttributes()
    {
        return array(
            'id'                => array(AttributeType::Number),
            'tokenId'           => array(AttributeType::Number),
            'handle'            => array(AttributeType::String, 'required' => true),
            'providerSettings'  => array(AttributeType::Mixed),
        );
    }

}