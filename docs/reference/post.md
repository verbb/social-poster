# Post

A post records one attempt to send an entry to a social media account, including the settings used and the provider’s response. You get posts from a [post query](docs:getting-elements/post-queries), which returns an array of `verbb\socialposter\elements\Post` objects.

```twig
{% set posts = craft.socialPoster.posts()
    .ownerId(entry.id)
    .all() %}

{% for post in posts %}
    <p>{{ post.getAccount().name ?? 'Deleted account' }}: {{ post.success ? 'Sent' : 'Failed' }}</p>
{% endfor %}
```

<span id="attributes"></span>

## Properties

::: reference
### `accountId`

**Type:** `int|null`

The ID of the account this post was sent from.
:::

::: reference
### `data`

**Type:** `array|null`

The full response data returned by the provider. When sending failed, this holds any error details the provider returned.
:::

::: reference
### `id`

**Type:** `int|null`

The post’s element ID.
:::

::: reference
### `owner`

**Type:** `craft\base\ElementInterface|null`

The element this post was sent for, usually an entry, or `null` when that element no longer exists. This reads `getOwner()`.
:::

::: reference
### `ownerId`

**Type:** `int|null`

The ID of the element this post was sent for.
:::

::: reference
### `ownerSiteId`

**Type:** `int|null`

The ID of the site the owner element was sent from.
:::

::: reference
### `ownerType`

**Type:** `string|null`

The class name of the owner element, such as `craft\elements\Entry`.
:::

::: reference
### `response`

**Type:** `array|null`

A short summary of the provider’s response. When sending failed, it holds the `statusCode` and `reasonPhrase` of the error.
:::

::: reference
### `settings`

**Type:** `array|null`

The settings used to send this post, such as its title, URL and message.
:::

::: reference
### `success`

**Type:** `bool|null`

Whether the post was sent to the provider successfully.
:::

::: reference
### `title`

**Type:** `string|null`

The post’s title, taken from the owner element’s title.
:::

## Methods

::: reference
### `getAccount()`

**Returns:** `verbb\socialposter\base\AccountInterface|null`

Returns the [Account](docs:reference/account) this post was sent from, or `null` when the account has been deleted.
:::
