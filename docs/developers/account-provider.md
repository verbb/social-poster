# Account Provider
You can register your own Account Provider to add support for other social media platforms, or even extend an existing Account Provider.

```php
namespace modules\sitemodule;

use craft\events\RegisterComponentTypesEvent;
use modules\sitemodule\MyAccountProvider;
use verbb\socialposter\services\Accounts;
use yii\base\Event;

Event::on(Accounts::class, Accounts::EVENT_REGISTER_ACCOUNT_TYPES, function(RegisterComponentTypesEvent $event) {
    $event->types[] = MyAccountProvider::class;
});
```

## Example
Create the following class to house your Account Provider logic.

```php
namespace modules\sitemodule;

use Craft;
use Throwable;
use verbb\socialposter\base\OAuthAccount;
use verbb\socialposter\models\Payload;
use verbb\socialposter\models\PostResponse;

use League\OAuth2\Client\Provider\SomeProvider;

class MyAccountProvider extends OAuthAccount
{
    // Static Methods
    // =========================================================================

    public static function displayName(): string
    {
        return 'My Account Provider';
    }

    public static function getOAuthProviderClass(): string
    {
        return SomeProvider::class;
    }


    // Properties
    // =========================================================================

    public static string $providerHandle = 'myAccountProvider';


    // Public Methods
    // =========================================================================

    public function getPrimaryColor(): ?string
    {
        return '#000000';
    }

    public function getIcon(): ?string
    {
        return '<svg>...</svg>';
    }

    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('my-module/my-account/settings', [
            'account' => $this,
        ]);
    }

    public function getPostSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('my-module/my-account/post-settings', [
            'account' => $this,
        ]);
    }

    public function sendPost(Payload $payload): PostResponse
    {
        try {
            // Construct your POST request according to the API
            $response = $this->request('POST', 'api/endpoint', [
                'json' => [
                    'text' => $payload->message,
                ],
            ]);

            return $this->getPostResponse($response);
        } catch (Throwable $e) {
            return $this->getPostExceptionResponse($e);
        }
    }
}
```

This is the minimum amount of implementation required for a typical account provider.

Social Poster account provider are built around the [Auth](https://github.com/verbb/auth) which in turn in built around [league/oauth2-client](https://github.com/thephpleague/oauth2-client). You can see that the `getOAuthProviderClass()` must return a `League\OAuth2\Client\Provider\AbstractProvider` class.
