<?php
namespace verbb\socialposter\accounts;

use verbb\socialposter\base\Account;
use verbb\socialposter\models\Payload;
use verbb\socialposter\models\PostResponse;

use Craft;
use craft\base\MissingComponentInterface;
use craft\base\MissingComponentTrait;

use yii\base\NotSupportedException;

class MissingAccount extends Account implements MissingComponentInterface
{
    // Traits
    // =========================================================================

    use MissingComponentTrait;


    // Static Methods
    // =========================================================================

    public static function displayName(): string
    {
        return Craft::t('social-poster', 'Missing Account');
    }

    public static function getOAuthProviderClass(): string
    {
        throw new NotSupportedException('getOAuthProviderClass() is not implemented.');
    }


    // Properties
    // =========================================================================

    public static string $providerHandle = 'missingAccount';


    // Public Methods
    // =========================================================================

    public function sendPost(Payload $payload): PostResponse
    {
        throw new NotSupportedException('sendPost() is not implemented.');
    }
}
