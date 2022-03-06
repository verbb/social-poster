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
    public array $providers = [];

}