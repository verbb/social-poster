# Upgrading from v3
While the [changelog](https://github.com/verbb/social-poster/blob/craft-4/CHANGELOG.md) is the most comprehensive list of changes, this guide provides high-level overview and organizes changes by category.

## Accounts & Providers
Social Poster v4 combines Accounts and Providers into a single concept - Accounts. This is to better handle multiple clients for the same social media provider, and multi-site handling.

### Re-connect Required
You'll be required to re-connect all your social media accounts again to gain new tokens, once you have moved your settings over from Providers to Accounts. This is to ensure new tokens are created using the [Auth module](https://github.com/verbb/auth). In some cases (for Facebook) we also store a long-lived token, which is then converted to a never-expiring Page Access Token, which helps with connectivity.

Be sure to also update the callback URL before you try to re-connect.

### New Callback URL
There's a new callback URL you'll be required to update in your OAuth clients for each provider. This is to fix some providers not allowing query strings in the callback URL, which some installs have with `usePathInfo` set to `false`. We now specify a site-based path for consistency.

Old | What to do instead
--- | ---
| `https://my-site.test/index.php?p=actions/social-poster/accounts/callback` | `https://my-site.test/social-poster/auth/callback`
| `https://my-site.test/actions/social-poster/accounts/callback` | `https://my-site.test/social-poster/auth/callback`
| `https://my-site.test/social-poster/accounts/callback` | `https://my-site.test/social-poster/auth/callback`

### Auth Module
We now offload all authentication tasks to the [Auth module](https://github.com/verbb/auth). We no longer store and manage tokens in this plugin, but through the Auth module. This won't mean anything to you as an end user, but it's good to know for custom development, if you need to access the raw tokens.

### Custom Provider
Any custom Providers will need to be ported across to be a custom Account. This is largely the same API, with some changes to ensure it works with the [Auth module](https://github.com/verbb/auth), but also to modernise typings for PHP 8.

```php
// Social Poster v2
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


// Social Poster v3
<?php
namespace myplugin\providers;

use verbb\socialposter\base\OAuthAccount;
use verbb\socialposter\models\Payload;
use verbb\socialposter\models\PostResponse;

use verbb\auth\providers\Disqus as DisqusProvider;

class Disqus extends OAuthAccount
{
    public static string $providerHandle = 'disqus';

    public static function getOAuthProviderClass(): string
    {
        return DisqusProvider::class;
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


## Plugin Settings
Some plugin settings have been removed.

Old | What to do instead
--- | ---
| `providers` | `accounts`


## Controllers
The following controller actions have been changed.

Old | What to do instead
--- | ---
| `social-poster/accounts/connect` | `social-poster/auth/connect`
| `social-poster/accounts/disconnect` | `social-poster/auth/disconnect`
| `social-poster/accounts/callback` | `social-poster/auth/callback`


## Events
The following events have been changed.

Old | What to do instead
--- | ---
| `Tokens::EVENT_BEFORE_SAVE_TOKEN` | See [Auth module](https://github.com/verbb/auth)
| `Tokens::EVENT_AFTER_SAVE_TOKEN` | See [Auth module](https://github.com/verbb/auth)
| `Tokens::EVENT_BEFORE_DELETE_TOKEN` | See [Auth module](https://github.com/verbb/auth)
| `Tokens::EVENT_AFTER_DELETE_TOKEN` | See [Auth module](https://github.com/verbb/auth)
| `AccountsController::EVENT_AFTER_OAUTH_CALLBACK` | See [Auth module](https://github.com/verbb/auth)

