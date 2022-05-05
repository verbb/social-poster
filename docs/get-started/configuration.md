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
        'providers' => [],
    ]
];
```

## Configuration options
- `pluginName` - If you wish to customise the plugin name.
- `hasCpSection` - Whether to have the plugin pages appear on the main CP sidebar menu.
- `enabledSections` - An array of section UIDs to enable social poster on. Use '\*' for all.
- `providers` - A collection of options for each provider.

### Providers
Supply your client configurations as per the below.

```php
'providers' => [
    'facebook' => [
        'oauth' => [
            'options' => [
                'clientId' => 'xxxxxxxxxxxx',
                'clientSecret' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
                'scope' => [
                    'some_scope',
                    'another_scope',
                ],
            ]
        ]
    ],
    'linkedin' => [
        'oauth' => [
            'options' => [
                'clientId' => 'xxxxxxxxxxxx',
                'clientSecret' => 'xxxxxxxxxxxx',
            ]
        ]
    ],
    'twitter' => [
        'oauth' => [
            'options' => [
                'clientId' => 'xxxxxxxxxxxx',
                'clientSecret' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
            ]
        ]
    ],
]
```

## Control Panel
You can also manage configuration settings through the Control Panel by visiting Settings → Social Poster.

## PATH_INFO
It's also a good idea to enable `PATH_INFO` in your project. See [Craft Guide](https://craftcms.com/guides/enabling-path-info). This is particularly important for the redirect URLs, as they need to be valid and match with what you put in each provider's app.

For example, with `PATH_INFO` off, you may get a redirect URL similar to:

`http://mysite.local/index.php?p=actions/social-poster/accounts/callback`

Which is completed valid, but Facebook and Twitter (and potentially others) have issues with the query string in the path. Turning on `PATH_INFO` would produce the same URL as:

`http://mysite.local/actions/social-poster/accounts/callback`

See the [Craft Guide](https://craftcms.com/guides/enabling-path-info) on how to enable this for your system setup.
