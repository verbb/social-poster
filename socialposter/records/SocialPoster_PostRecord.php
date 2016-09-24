<?php
namespace Craft;

class SocialPoster_PostRecord extends BaseRecord
{
    // Public Methods
    // =========================================================================

    public function getTableName()
    {
        return 'socialposter_posts';
    }

    public function defineRelations()
    {
        return array(
            'element'  => array(static::BELONGS_TO, 'ElementRecord', 'required' => true, 'onDelete' => static::CASCADE),
        );
    }

    public function scopes()
    {
        return array(
            'ordered' => array('order' => 'dateCreated asc'),
        );
    }


    // Protected Methods
    // =========================================================================

    protected function defineAttributes()
    {
        return array(
            'handle'            => array(AttributeType::String, 'required' => true),
            'providerSettings'  => array(AttributeType::Mixed),
            'success'           => array(AttributeType::Bool),
            'response'          => array(AttributeType::Mixed),
            'data'              => array(AttributeType::Mixed),
        );
    }
}
