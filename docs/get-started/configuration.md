# Configuration
Create a `social-poster.php` file under your `/config` directory with the following options available to you. You can also use multi-environment options to change these per environment.

The below shows the defaults already used by Social Poster, so you don't need to add these options unless you want to modify the values.

```php
<?php

return [
    '*' => [
        'pluginName' => 'Social Poster',
        'hasCpSection' => false,
        'enabledSections' => '*',
        'accounts' => [],
    ]
];
```

## Configuration options
- `pluginName` - If you wish to customise the plugin name.
- `hasCpSection` - Whether to have the plugin pages appear on the main CP sidebar menu.
- `enabledSections` - An array of section UIDs to enable social poster on. Use '\*' for all.
- `accounts` - A collection of options for each account.

### Accounts
Supply your client configurations as per the below. The `key` for each item should be the account `handle`.

```php
return [
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
];
```

## Control Panel
You can also manage configuration settings through the Control Panel by visiting Settings → Social Poster.
