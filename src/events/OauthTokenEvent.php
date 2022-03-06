<?php
namespace verbb\socialposter\events;

use verbb\socialposter\models\Token;

use yii\base\Event;

class OauthTokenEvent extends Event
{
    // Properties
    // =========================================================================

    public Token $token;

}
