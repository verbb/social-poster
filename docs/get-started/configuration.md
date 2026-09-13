# Configuration

You can customise Social Poster’s settings using a PHP configuration file. This is optional: each setting has a default, so you only need to include the values you want to change.

To override a setting, create `social-poster.php` in your Craft project’s `/config` directory and return an array of setting names and values. For example, the following will change the name displayed in the control panel:

```php
<?php

return [
    'pluginName' => 'Social Poster Tools',
];
```

All other settings keep their defaults. Add any further settings you want to change to the same array. The options below explain the available settings and their defaults.

## Configuration Options

::: reference
### `pluginName`

**Type:** `string` · **Default:** `'Social Poster'`

If you wish to customise the plugin name.
:::


::: reference
### `hasCpSection`

**Type:** `bool` · **Default:** `false`

Whether to have the plugin pages appear on the main CP sidebar menu.
:::


::: reference
### `enabledSections`

**Type:** `mixed` · **Default:** `'*'`

An array of section UIDs to enable social poster on. Use '\*' for all.
:::


::: reference
### `redirectUri`

**Type:** `string|null` · **Default:** `null`

Optionally override the OAuth redirect URI for detached or multi-domain setups. This applies to all accounts.
:::

- `accounts` - A collection of options for each account.

### Redirect URI Override
By default, Social Poster will continue to use its legacy callback URI. If you need to use a different callback URI, such as for detached domains or an `/actions/...` callback, set `redirectUri` at the plugin level.

```php
'redirectUri' => 'https://craft.example.com/actions/social-poster/auth/callback',
```

### Accounts
Supply your client configurations as per the below. The `key` for each item should be the account `handle`.

```php
return [
    '*' => [
        // ...
        'accounts' => [
            'facebook' => [
                'enabled' => true,
                'autoPost' => false,
                'clientId' => '••••••••••••••••••••••••••••',
                'clientSecret' => '••••••••••••••••••••••••••••',

                // Add in any additional OAuth scopes
                'scopes' => [
                    'business_management',
                ],

                // Add in any additional OAuth authorization options, used when redirecting
                // to the provider to start the OAuth authorization process
                'authorizationOptions' => [
                    'extra' => 'value',
                ],
            ],
            'linkedIn' => [
                'clientId' => '••••••••••••••••••••••••••••',
                'clientSecret' => '••••••••••••••••••••••••••••',
            ],
            'twitter' => [
                'clientId' => '••••••••••••••••••••••••••••',
                'clientSecret' => '••••••••••••••••••••••••••••',
            ],
        ],
    ],
];
```

## Control Panel
You can also manage configuration settings through the Control Panel by visiting Settings → Social Poster.
