<?php
namespace verbb\socialposter\models;

use craft\base\Model;

class Settings extends Model
{
    // Properties
    // =========================================================================

    public string $pluginName = 'Social Poster';
    public bool $hasCpSection = false;
    public mixed $enabledSections = '*';
    

    // Public Methods
    // =========================================================================

    public function __construct(array $config = [])
    {
        // Config normalization
        if (array_key_exists('providers', $config)) {
            unset($config['providers']);
        }

        parent::__construct($config);
    }

}