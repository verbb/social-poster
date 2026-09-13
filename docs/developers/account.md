# Account
Whenever you're dealing with an account in your template, you're actually working with a `Account` object.

<span id="attributes"></span>

## Properties

::: reference
### `name`

**Type:** `string|null`

The name of the account.
:::

::: reference
### `handle`

**Type:** `string|null`

The handle of the account.
:::

::: reference
### `primaryColor`

**Type:** `string|null`

The primary brand color of the provider connected.
:::

::: reference
### `icon`

**Type:** `string|null`

The SVG icon of the account provider connected.
:::

::: reference
### `providerName`

**Type:** `string`

The name of the account provider connected.
:::


## Methods

::: reference
### `isConfigured()`

Whether the account provider has been configured.
:::

::: reference
### `isConnected()`

**Returns:** `bool`

Whether the account provider has been connected and has a token.
:::

::: reference
### `getToken()`

The access token for a account provider.
:::
