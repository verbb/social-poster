<?php
namespace Craft;

class SocialPoster_AccountRecord extends BaseRecord
{
    // Public Methods
    // =========================================================================

    public function getTableName()
    {
        return 'socialposter_accounts';
    }

    public function defineIndexes()
    {
        return array(
            array('columns' => array('handle'), 'unique' => true),
        );
    }


    // Protected Methods
    // =========================================================================

    protected function defineAttributes()
    {
        return array(
            'tokenId'           => array(AttributeType::Number),
            'handle'            => array(AttributeType::String, 'required' => true),
            'providerSettings'  => array(AttributeType::Mixed),
        );
    }
}
