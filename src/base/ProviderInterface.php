<?php
namespace verbb\socialposter\base;

use verbb\socialposter\models\Account;

use craft\base\SavableComponentInterface;
use craft\web\Response;

interface ProviderInterface extends SavableComponentInterface
{
    // Public Methods
    // =========================================================================

    public function getName(): string;
    public function getOauthProvider();
    public function oauthConnect();
    public function oauthCallback();
    public function sendPost($account, $content);
}
