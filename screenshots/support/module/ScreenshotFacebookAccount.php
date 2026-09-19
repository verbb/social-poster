<?php
namespace modules\socialposterscreenshots;

use verbb\auth\models\Token;
use verbb\socialposter\accounts\Facebook;

class ScreenshotFacebookAccount extends Facebook
{
    public static function displayName(): string
    {
        return 'Facebook';
    }

    public function getToken(): ?Token
    {
        return new Token();
    }
}
