<?php
namespace verbb\socialposter\events;

use yii\base\Event;

class AccountEvent extends Event
{
    // Properties
    // =========================================================================

    public $account;
    public $isNew = false;

}
