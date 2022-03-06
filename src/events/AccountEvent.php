<?php
namespace verbb\socialposter\events;

use verbb\socialposter\models\Account;

use yii\base\Event;

class AccountEvent extends Event
{
    // Properties
    // =========================================================================

    public Account $account;
    public bool $isNew = false;

}
