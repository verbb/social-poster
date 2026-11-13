# Custom Providers

A provider connects Social Poster accounts to a social media service, such as Facebook or LinkedIn. Create a custom provider to post to a service Social Poster doesn’t include, or to change how an existing provider behaves by extending its class.

## Create the Provider Class

Extend `verbb\socialposter\base\OAuthAccount`. Social Poster uses the [Auth](https://github.com/verbb/auth) module for OAuth, which builds on [league/oauth2-client](https://github.com/thephpleague/oauth2-client), so `getOAuthProviderClass()` returns an Auth provider or any `League\OAuth2\Client\Provider\AbstractProvider` class. The static `$providerHandle` property identifies the provider, and `sendPost()` sends the post.

This class, saved as `modules/sitemodule/Example.php`, posts the message to an `api/posts` endpoint using Auth’s generic provider. Replace the endpoint, request body and OAuth settings with those of your service.

```php
<?php
namespace modules\sitemodule;

use Craft;
use Throwable;
use verbb\auth\providers\Generic as GenericProvider;
use verbb\socialposter\base\OAuthAccount;
use verbb\socialposter\models\Payload;
use verbb\socialposter\models\PostResponse;

class Example extends OAuthAccount
{
    // Static Methods
    // =========================================================================

    public static function displayName(): string
    {
        return 'Example';
    }

    public static function getOAuthProviderClass(): string
    {
        return GenericProvider::class;
    }


    // Properties
    // =========================================================================

    public static string $providerHandle = 'example';


    // Public Methods
    // =========================================================================

    public function getOAuthProviderConfig(): array
    {
        $config = parent::getOAuthProviderConfig();
        $config['urlAuthorize'] = 'https://example.com/oauth/authorize';
        $config['urlAccessToken'] = 'https://example.com/oauth/token';
        $config['urlResourceOwnerDetails'] = 'https://example.com/api/me';

        return $config;
    }

    public function getPrimaryColor(): ?string
    {
        return '#1d4ed8';
    }

    public function getIcon(): ?string
    {
        return '<svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg>';
    }

    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('sitemodule/example/settings', [
            'account' => $this,
        ]);
    }

    public function getPostSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('sitemodule/example/post-settings', [
            'account' => $this,
            'assetFieldOptions' => $this->getAssetFieldOptions(),
        ]);
    }

    public function getInputHtml($context): string
    {
        $context['account'] = $this;
        $context['assetFieldOptions'] = $this->getAssetFieldOptions();

        return Craft::$app->getView()->renderTemplate('sitemodule/example/input', $context);
    }

    public function getResponseUrl($data): ?string
    {
        return $data['url'] ?? null;
    }

    public function sendPost(Payload $payload): PostResponse
    {
        try {
            $response = $this->sendRequest($payload->element, 'api/posts', [
                'text' => $payload->message,
            ]);

            return $this->getPostResponse($response);
        } catch (Throwable $e) {
            return $this->getPostExceptionResponse($e);
        }
    }
}
```

## Register the Provider

Social Poster finds providers through the `registerAccountTypes` event. Register your class from your module or plugin’s `init()` method.

```php
use craft\events\RegisterComponentTypesEvent;
use modules\sitemodule\Example;
use verbb\socialposter\services\Accounts;
use yii\base\Event;

Event::on(Accounts::class, Accounts::EVENT_REGISTER_ACCOUNT_TYPES, function(RegisterComponentTypesEvent $event) {
    $event->types[] = Example::class;
});
```

## Add the Templates

The built-in templates are looked up by provider handle, so a custom provider renders its own settings, post settings and entry sidebar templates. Register a control panel template root for `sitemodule` in your module, as described in Craft’s [template root documentation](https://craftcms.com/docs/5.x/extend/template-roots.html), and add `settings.html`, `post-settings.html` and `input.html` to its `example` folder. The Twitter templates in Social Poster’s `src/templates/accounts/_types/twitter/` folder are a good starting point.

## Methods

A provider class extends `verbb\socialposter\base\OAuthAccount` and overrides these methods as needed.

::: reference
### `displayName()`

**Returns:** `string`

Returns the provider’s name, shown in the control panel when choosing an account’s provider. This method is static.
:::

::: reference
### `getDefaultScopes()`

**Returns:** `array`

Returns the OAuth scopes the provider always requests, added to the Auth provider’s defaults and the account’s configured `scopes`. Returns an empty array by default.
:::

::: reference
### `getIcon()`

**Returns:** `string|null`

Returns the SVG markup for the provider’s icon, or `null` for no icon. The default looks up the icon for `$providerHandle` in the [Auth](https://github.com/verbb/auth) module.
:::

::: reference
### `getInputHtml()`

**Arguments:** `context` (`array`), the sidebar’s template variables, including the entry being edited.

**Returns:** `string`

Returns the HTML for the account’s controls in the entry sidebar, where editors write and send a post.
:::

::: reference
### `getOAuthProviderClass()`

**Returns:** `string`

Returns the class name of the [Auth](https://github.com/verbb/auth) or league/oauth2-client provider that handles OAuth for this account. This method is static and required.
:::

::: reference
### `getOAuthProviderConfig()`

**Returns:** `array`

Returns the configuration the OAuth provider is created with. Start with `parent::getOAuthProviderConfig()`, which supplies the client ID, secret and redirect URI.
:::

::: reference
### `getPostSettingsHtml()`

**Returns:** `string|null`

Returns the HTML for the account’s default post settings, such as the default message, shown when editing the account.
:::

::: reference
### `getPrimaryColor()`

**Returns:** `string|null`

Returns the provider’s brand colour as a hex value, or `null` for none.
:::

::: reference
### `getResponseUrl()`

**Arguments:** `data` (`array|null`), the provider’s response data saved on the post.

**Returns:** `string|null`

Returns the URL of the published post on the provider, worked out from the response data, or `null` when there isn’t one. The post’s edit screen links to it.
:::

::: reference
### `getSettingsHtml()`

**Returns:** `string|null`

Returns the HTML for the account’s provider settings, such as the client ID and secret, shown when editing the account.
:::

::: reference
### `isConfigured()`

**Returns:** `bool`

Returns whether the account has the settings it needs. OAuth accounts return `true` when a client ID and secret are set.
:::

::: reference
### `sendPost()`

**Arguments:** `payload` (`verbb\socialposter\models\Payload`), with the `element` being posted and its `title`, `url`, `message` and `picture`.

**Returns:** `verbb\socialposter\models\PostResponse`

Returns the result of sending a post to the provider. Use `sendRequest()` to make the request, so the `beforeSendPost` and `afterSendPost` events fire, then return `getPostResponse()` on success or `getPostExceptionResponse()` when the request throws an exception. This method is required.
:::
