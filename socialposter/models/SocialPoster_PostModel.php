<?php
namespace Craft;

class SocialPoster_PostModel extends BaseModel
{
    // Public Methods
    // =========================================================================

    public function getElement()
    {
        if ($this->elementId) {
            return craft()->elements->getElementById($this->elementId);
        }
    }

    public function getUrl()
    {
        $data = $this->data;

        if (isset($data)) {
            if ($this->handle == 'facebook' && isset($data['id'])) {
                return 'https://facebook.com/' . $data['id'];
            }

            if ($this->handle == 'twitter' && isset($data['id'])) {
                return 'https://twitter.com/' . $data['user']['screen_name'] . '/status/' . $data['id'];
            }

            if ($this->handle == 'linkedin' && isset($data['updateUrl'])) {
                return $data['updateUrl'];
            }
        }
    }


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
            'data'              => array(AttributeType::Mixed),
            'dateCreated'       => array(AttributeType::DateTime),
            'dateUpdated'       => array(AttributeType::DateTime),
        );
    }

}