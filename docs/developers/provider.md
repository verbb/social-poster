# Provider
You can register additional social media providers to post to by registering your class. Then, you'll need to create your provider class to implement the `ProviderInterface`.

## The `registerProviderTypes` event

```php
use verbb\socialposter\events\RegisterProviderTypesEvent;
use verbb\socialposter\services\Providers;
use yii\base\Event;

Event::on(Providers::class, Providers::EVENT_REGISTER_PROVIDER_TYPES, function(RegisterProviderTypesEvent $e) {
    $e->providerTypes[] = MyProvider::class;
});
```

## Provider Class

Create a new class for your provider. You'll want to pick an existing OAuth 1/2 compatible implementation from [League](https://github.com/thephpleague/oauth2-client/blob/master/docs/providers/thirdparty.md).

```php
<?php
namespace myplugin\providers;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\base\Provider;
use verbb\socialposter\helpers\SocialPosterHelper;
use verbb\socialposter\models\Account;

use Craft;
use craft\helpers\Json;

use League\OAuth2\Client\Provider\Provider as OauthProvider;

class MyProvider extends Provider
{
    public function getName()
    {
        return 'Provider';
    }

    public function getOauthProvider()
    {
        $config = $this->getOauthProviderConfig();

        return new OauthProvider($config['options']);
    }

    public function sendPost($account, $content)
    {
        try {
            $token = $account->getToken();
            $client = $this->getClient($token);

            // Construct your POST request according to the API
            $response = $client->post('api/endpoint', [
                'form_params' => [
                    'message' => $content['message'],
                ]
            ]);

            return $this->getPostResponse($response);
        } catch (\Throwable $e) {
            return $this->getPostExceptionResponse($e);
        }
    }
}
```