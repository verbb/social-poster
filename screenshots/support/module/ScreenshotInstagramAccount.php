<?php
namespace modules\socialposterscreenshots;

use verbb\auth\models\Token;
use verbb\socialposter\accounts\Instagram;

class ScreenshotInstagramAccount extends Instagram
{
    public static function displayName(): string
    {
        return 'Instagram';
    }

    public function getToken(): ?Token
    {
        return new Token();
    }
}
