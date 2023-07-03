<?php
namespace verbb\socialposter\base;

use Craft;
use craft\helpers\UrlHelper;

use verbb\auth\Auth;
use verbb\auth\base\OAuthProviderInterface;
use verbb\auth\base\OAuthProviderTrait;
use verbb\auth\models\Token;

abstract class OAuthAccount extends Account implements OAuthProviderInterface
{
    // Traits
    // =========================================================================

    use OAuthProviderTrait;
    

    // Public Methods
    // =========================================================================

    public function settingsAttributes(): array
    {
        // These won't be picked up in a Trait
        $attributes = parent::settingsAttributes();
        $attributes[] = 'clientId';
        $attributes[] = 'clientSecret';

        return $attributes;
    }

    public function defineRules(): array
    {
        $rules = parent::defineRules();

        $rules[] = [
            ['clientId', 'clientSecret'], 'required', 'when' => function($model) {
                return $model->enabled;
            },
        ];

        return $rules;
    }

    public function isConfigured(): bool
    {
        return $this->clientId && $this->clientSecret;
    }

    public function isConnected(): bool
    {
        return (bool)$this->getToken();
    }

    public function getRedirectUri(): ?string
    {
        $siteId = Craft::$app->getSites()->getCurrentSite()->id ?? Craft::$app->getSites()->getPrimarySite()->id;

        // We should always use the primary site for the redirect
        return UrlHelper::siteUrl('social-poster/auth/callback', null, null, $siteId);
    }

    public function getAuthorizationUrlOptions(): array
    {
        return [];
    }

    public function getToken(): ?Token
    {
        if ($this->id) {
            return Auth::$plugin->getTokens()->getTokenByOwnerReference('social-poster', $this->id);
        }

        return null;
    }
}