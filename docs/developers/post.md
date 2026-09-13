# Post
Whenever you're dealing with a post in your template, you're actually working with a `Post` object.

<span id="attributes"></span>

## Properties

::: reference
### `id`

**Type:** `int|null`

ID of the post.
:::

::: reference
### `accountId`

**Type:** `int|null`

The account ID this post was made from.
:::

::: reference
### `ownerId`

**Type:** `int|null`

The entry ID this post was made on.
:::

::: reference
### `ownerSiteId`

**Type:** `int|null`

The site ID this post was made on.
:::

::: reference
### `owner`

**Type:** `ActiveQuery`

[Entry](https://docs.craftcms.com/api/v4/craft-elements-entry.html) this post was made on.
:::

::: reference
### `ownerType`

**Type:** `string|null`

The class name of the element this post was made on.
:::

::: reference
### `settings`

**Type:** `array|null`

Serialized content that was used to send out this post.
:::

::: reference
### `success`

**Type:** `bool|null`

Boolean whether the post was successfully sent to social media platforms.
:::

::: reference
### `response`

**Type:** `array|null`

A short response from the social media provider.
:::

::: reference
### `data`

**Type:** `array|null`

A full response from the social media provider.
:::


## Methods

::: reference
### `getAccount()`

**Returns:** `verbb\socialposter\base\AccountInterface|null`

Returns the account this post was made from.
:::
