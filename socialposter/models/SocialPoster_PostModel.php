<?php
namespace Craft;

class SocialPoster_PostModel extends BaseModel
{
    // Protected Methods
    // =========================================================================

    protected function defineAttributes()
    {
        return array(
            'id'                => array(AttributeType::Number),
            'elementId'         => array(AttributeType::Number),
            'handle'            => array(AttributeType::String, 'required' => true),
            'providerSettings'  => array(AttributeType::Mixed),
            'success'           => array(AttributeType::Bool),
            'response'          => array(AttributeType::Mixed),
            'dateCreated'       => array(AttributeType::DateTime),
            'dateUpdated'       => array(AttributeType::DateTime),
        );
    }

}