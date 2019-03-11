<?php
namespace verbb\socialposter\models;

use craft\base\Model;

class Settings extends Model
{
    // Public Properties
    // =========================================================================

    public $pluginName = 'Social Poster';
    public $hasCpSection = false;
    public $enabledSections = '*';
    public $providers = [];

}