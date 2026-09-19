<?php
namespace modules\socialposterscreenshots;

use verbb\auth\models\Token;
use verbb\socialposter\accounts\LinkedIn;

class ScreenshotLinkedInAccount extends LinkedIn
{
    public static function displayName(): string
    {
        return 'LinkedIn';
    }

    public function getToken(): ?Token
    {
        return new Token();
    }
}
