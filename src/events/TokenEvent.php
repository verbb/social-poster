<?php
namespace verbb\socialposter\events;

use verbb\socialposter\models\Token;

use yii\base\Event;

class TokenEvent extends Event
{
    // Properties
    // =========================================================================

    public Token $token;
    public bool $isNew = false;

}
