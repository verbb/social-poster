# Account
Whenever you're dealing with an account in your template, you're actually working with a `Account` object.

## Attributes

Attribute | Description
--- | ---
`name` | The name of the account.
`handle` | The handle of the account.
`primaryColor` | The primary brand color of the provider connected.
`icon` | The SVG icon of the account provider connected.
`providerName` | The name of the account provider connected.

## Methods

Method | Description
--- | ---
`isConfigured()` | Whether the account provider has been configured.
`isConnected()` | Whether the account provider has been connected and has a token.
`getToken()` | The access token for a account provider.
