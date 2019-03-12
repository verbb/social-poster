# Configuration

Create an `social-poster.php` file under your `/config` directory with the following options available to you. You can also use multi-environment options to change these per environment.

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

### Configuration options

- `pluginName` - If you wish to customise the plugin name.
- `hasCpSection` - Whether to have the plugin pages appear on the main CP sidebar menu.
- `enabledSections` - An array of section UIDs to enable social poster on. Use '\*' for all.
- `providers` - A collection of options for each provider.

#### Providers
Supply your client configurations as per the below.

```php
<?php

return [
    'providers' => [
        'facebook' => [
            'oauth' => [
                'options' => [
                    'clientId' => 'xxxxxxxxxxxx',
                    'clientSecret' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
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
];
```

## Control Panel

You can also manage configuration settings through the Control Panel by visiting Settings → Social Poster.
