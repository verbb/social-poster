<?php
namespace verbb\socialposter\events;

use verbb\socialposter\base\AccountInterface;

use yii\base\Event;

class AccountEvent extends Event
{
    // Properties
    // =========================================================================

    public AccountInterface $account;
    public bool $isNew = false;

}
