<?php
namespace modules\socialposterscreenshots;

use verbb\auth\models\Token;
use verbb\socialposter\accounts\Twitter;

class ScreenshotTwitterAccount extends Twitter
{
    public static function displayName(): string
    {
        return 'Twitter';
    }

    public function getToken(): ?Token
    {
        return new Token();
    }
}
