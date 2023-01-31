<?php
namespace verbb\socialposter\base;

use verbb\socialposter\models\Payload;
use verbb\socialposter\models\PostResponse;

use craft\base\SavableComponentInterface;

interface AccountInterface extends SavableComponentInterface
{
    // Public Methods
    // =========================================================================

    public static function getOAuthProviderClass(): string;
    public function sendPost(Payload $payload): PostResponse;

}
