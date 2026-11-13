# Account

An account is a social media account that Social Poster sends posts to, such as a Facebook page or a LinkedIn profile. In templates you’ll usually reach an account through a [Post](docs:reference/post), using `post.getAccount()`.

```twig
{% set account = post.getAccount() %}

{% if account %}
    <span style="color: {{ account.primaryColor }}">{{ account.icon | raw }}</span>
    {{ account.name }} ({{ account.providerName }})
{% endif %}
```

<span id="attributes"></span>

## Properties

::: reference
### `autoPost`

**Type:** `bool|null`

Whether entries are posted to this account when they’re saved. Editors can change this for each entry in the entry sidebar.
:::

::: reference
### `enabled`

**Type:** `bool|null`

Whether the account is enabled.
:::

::: reference
### `handle`

**Type:** `string|null`

The account’s handle.
:::

::: reference
### `icon`

**Type:** `string|null`

The SVG icon markup for the account’s provider, or `null` when the provider has no icon. Output it with the `raw` filter. This reads `getIcon()`.
:::

::: reference
### `id`

**Type:** `int|null`

The account’s ID.
:::

::: reference
### `name`

**Type:** `string|null`

The account’s name, as entered in the control panel.
:::

::: reference
### `primaryColor`

**Type:** `string|null`

The brand colour of the account’s provider as a hex value, or `null` when it has none. This reads `getPrimaryColor()`.
:::

::: reference
### `providerName`

**Type:** `string`

The name of the account’s provider, such as `Facebook`. This reads `getProviderName()`.
:::

## Methods

::: reference
### `getToken()`

**Returns:** `verbb\auth\models\Token|null`

Returns the OAuth access token stored for this account, or `null` when the account hasn’t been connected.
:::

::: reference
### `isConfigured()`

**Returns:** `bool`

Returns whether the account’s provider settings, such as its client ID and secret, are filled in.
:::

::: reference
### `isConnected()`

**Returns:** `bool`

Returns whether the account has been connected to its provider and has a stored token.
:::
