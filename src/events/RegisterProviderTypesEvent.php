<?php
namespace verbb\socialposter\events;

use yii\base\Event;

class RegisterProviderTypesEvent extends Event
{
    // Properties
    // =========================================================================

    public array $providerTypes = [];
}
