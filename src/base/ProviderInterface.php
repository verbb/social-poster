<?php
namespace verbb\socialposter\base;

use craft\base\SavableComponentInterface;

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
