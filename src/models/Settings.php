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
    public ?string $redirectUri = null;
    

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

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['pluginName'], 'trim'];
        $rules[] = [['pluginName'], 'required'];
        $rules[] = [['pluginName'], 'string', 'max' => 52];

        return $rules;
    }

}
