# Post Queries

You can fetch posts in your templates or PHP code using **post queries**.

::: code
```twig Twig
{# Create a new post query #}
{% set myQuery = craft.socialPoster.posts() %}
```

```php PHP
// Create a new post query
$myQuery = \verbb\socialposter\elements\Post::find();
```
:::

Once you’ve created a post query, you can set parameters on it to narrow down the results, and then execute it by calling `.all()`. An array of [Post](docs:reference/post) objects will be returned.

See [Element Queries](https://craftcms.com/docs/5.x/development/element-queries.html) in the Craft docs to learn how element queries work.

## Example

You can display the posts sent for an entry by doing the following.

1. Create a post query with `craft.socialPoster.posts()`.
2. Set the [ownerId](#ownerid) and [limit](#limit) parameters on it.
3. Fetch the posts with `.all()`.
4. Loop through the posts using a [for](https://twig.symfony.com/doc/3.x/tags/for.html) tag to output their contents.

```twig
{# Create a post query with the 'ownerId' and 'limit' parameters #}
{% set postsQuery = craft.socialPoster.posts()
    .ownerId(entry.id)
    .limit(10) %}

{# Fetch the posts #}
{% set posts = postsQuery.all() %}

{# Display their contents #}
{% for post in posts %}
    <p>{{ post.getAccount().name ?? 'Deleted account' }}, {{ post.dateCreated|date('short') }}: {{ post.success ? 'Sent' : 'Failed' }}</p>
{% endfor %}
```

## Parameters

Post queries support the following parameters.

::: reference
### `accountId`

Narrows the query results to posts sent from the accounts with the given IDs.

| Value | Fetches posts… |
| --- | --- |
| `1` | sent from the account with an ID of 1. |
| `[1, 2]` | sent from the account with an ID of 1 or 2. |
| `'not 1'` | not sent from the account with an ID of 1. |

::: code
```twig Twig
{# Fetch posts sent from the account with an ID of 1 #}
{% set posts = craft.socialPoster.posts()
    .accountId(1)
    .all() %}
```

```php PHP
// Fetch posts sent from the account with an ID of 1
$posts = \verbb\socialposter\elements\Post::find()
    ->accountId(1)
    ->all();
```
:::
:::

::: reference
### `asArray`

Causes the query to return matching posts as arrays of data, rather than [Post](docs:reference/post) objects.

::: code
```twig Twig
{# Fetch posts as arrays #}
{% set posts = craft.socialPoster.posts()
    .asArray()
    .all() %}
```

```php PHP
// Fetch posts as arrays
$posts = \verbb\socialposter\elements\Post::find()
    ->asArray()
    ->all();
```
:::
:::

::: reference
### `dateCreated`

Narrows the query results based on when the posts were created, which is when they were sent.

| Value | Fetches posts… |
| --- | --- |
| `'>= 2026-04-01'` | created on or after 1 April 2026. |
| `'< 2026-05-01'` | created before 1 May 2026. |
| `['and', '>= 2026-04-01', '< 2026-05-01']` | created during April 2026. |

::: code
```twig Twig
{# Fetch posts sent last month #}
{% set start = date('first day of last month')|atom %}
{% set end = date('first day of this month')|atom %}

{% set posts = craft.socialPoster.posts()
    .dateCreated(['and', ">= #{start}", "< #{end}"])
    .all() %}
```

```php PHP
// Fetch posts sent last month
$start = (new \DateTime('first day of last month'))->format(\DateTime::ATOM);
$end = (new \DateTime('first day of this month'))->format(\DateTime::ATOM);

$posts = \verbb\socialposter\elements\Post::find()
    ->dateCreated(['and', ">= {$start}", "< {$end}"])
    ->all();
```
:::
:::

::: reference
### `dateUpdated`

Narrows the query results based on when the posts were last updated.

| Value | Fetches posts… |
| --- | --- |
| `'>= 2026-04-01'` | updated on or after 1 April 2026. |
| `'< 2026-05-01'` | updated before 1 May 2026. |

::: code
```twig Twig
{# Fetch posts updated in the last week #}
{% set lastWeek = date('1 week ago')|atom %}

{% set posts = craft.socialPoster.posts()
    .dateUpdated(">= #{lastWeek}")
    .all() %}
```

```php PHP
// Fetch posts updated in the last week
$lastWeek = (new \DateTime('1 week ago'))->format(\DateTime::ATOM);

$posts = \verbb\socialposter\elements\Post::find()
    ->dateUpdated(">= {$lastWeek}")
    ->all();
```
:::
:::

::: reference
### `fixedOrder`

Causes the query results to be returned in the order specified by [id](#id).

::: code
```twig Twig
{# Fetch posts in a specific order #}
{% set posts = craft.socialPoster.posts()
    .id([1, 2, 3, 4, 5])
    .fixedOrder()
    .all() %}
```

```php PHP
// Fetch posts in a specific order
$posts = \verbb\socialposter\elements\Post::find()
    ->id([1, 2, 3, 4, 5])
    ->fixedOrder()
    ->all();
```
:::
:::

::: reference
### `id`

Narrows the query results based on the posts’ IDs.

| Value | Fetches posts… |
| --- | --- |
| `1` | with an ID of 1. |
| `'not 1'` | not with an ID of 1. |
| `[1, 2]` | with an ID of 1 or 2. |
| `['not', 1, 2]` | not with an ID of 1 or 2. |

::: code
```twig Twig
{# Fetch the posts with an ID of 1 or 2 #}
{% set posts = craft.socialPoster.posts()
    .id([1, 2])
    .all() %}
```

```php PHP
// Fetch the posts with an ID of 1 or 2
$posts = \verbb\socialposter\elements\Post::find()
    ->id([1, 2])
    ->all();
```
:::

::: tip
Combine this with [fixedOrder](#fixedorder) to return the results in a specific order.
:::
:::

::: reference
### `inReverse`

Causes the query results to be returned in reverse order.

::: code
```twig Twig
{# Fetch posts in reverse order #}
{% set posts = craft.socialPoster.posts()
    .inReverse()
    .all() %}
```

```php PHP
// Fetch posts in reverse order
$posts = \verbb\socialposter\elements\Post::find()
    ->inReverse()
    ->all();
```
:::
:::

::: reference
### `limit`

Determines the number of posts that should be returned.

::: code
```twig Twig
{# Fetch up to 10 posts #}
{% set posts = craft.socialPoster.posts()
    .limit(10)
    .all() %}
```

```php PHP
// Fetch up to 10 posts
$posts = \verbb\socialposter\elements\Post::find()
    ->limit(10)
    ->all();
```
:::
:::

::: reference
### `offset`

Determines how many posts should be skipped in the results.

::: code
```twig Twig
{# Fetch all posts except the first 3 #}
{% set posts = craft.socialPoster.posts()
    .offset(3)
    .all() %}
```

```php PHP
// Fetch all posts except the first 3
$posts = \verbb\socialposter\elements\Post::find()
    ->offset(3)
    ->all();
```
:::
:::

::: reference
### `orderBy`

Determines the order that the posts should be returned in.

::: code
```twig Twig
{# Fetch the most recently sent posts first #}
{% set posts = craft.socialPoster.posts()
    .orderBy('elements.dateCreated desc')
    .all() %}
```

```php PHP
// Fetch the most recently sent posts first
$posts = \verbb\socialposter\elements\Post::find()
    ->orderBy('elements.dateCreated desc')
    ->all();
```
:::
:::

::: reference
### `ownerId`

Narrows the query results to posts sent for the elements with the given IDs, usually entries.

| Value | Fetches posts… |
| --- | --- |
| `1` | sent for the element with an ID of 1. |
| `[1, 2]` | sent for the element with an ID of 1 or 2. |
| `'not 1'` | not sent for the element with an ID of 1. |

::: code
```twig Twig
{# Fetch posts sent for the current entry #}
{% set posts = craft.socialPoster.posts()
    .ownerId(entry.id)
    .all() %}
```

```php PHP
// Fetch posts sent for the current entry
$posts = \verbb\socialposter\elements\Post::find()
    ->ownerId($entry->id)
    ->all();
```
:::
:::

::: reference
### `ownerSiteId`

Narrows the query results to posts sent from the sites with the given IDs.

| Value | Fetches posts… |
| --- | --- |
| `1` | sent from the site with an ID of 1. |
| `[1, 2]` | sent from the site with an ID of 1 or 2. |

::: code
```twig Twig
{# Fetch posts sent for an entry from the site with an ID of 2 #}
{% set posts = craft.socialPoster.posts()
    .ownerId(entry.id)
    .ownerSiteId(2)
    .all() %}
```

```php PHP
// Fetch posts sent for an entry from the site with an ID of 2
$posts = \verbb\socialposter\elements\Post::find()
    ->ownerId($entry->id)
    ->ownerSiteId(2)
    ->all();
```
:::
:::

::: reference
### `status`

Narrows the query results based on the posts’ statuses. Posts are enabled when they’re created, so most queries don’t need this. Pass `null` to fetch posts regardless of status.

| Value | Fetches posts… |
| --- | --- |
| `'enabled'` _(default)_ | that are enabled. |
| `'disabled'` | that are disabled. |
| `null` | regardless of status. |

::: code
```twig Twig
{# Fetch posts regardless of status #}
{% set posts = craft.socialPoster.posts()
    .status(null)
    .all() %}
```

```php PHP
// Fetch posts regardless of status
$posts = \verbb\socialposter\elements\Post::find()
    ->status(null)
    ->all();
```
:::
:::

::: reference
### `uid`

Narrows the query results based on the posts’ UIDs.

::: code
```twig Twig
{# Fetch the post by its UID #}
{% set post = craft.socialPoster.posts()
    .uid('xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx')
    .one() %}
```

```php PHP
// Fetch the post by its UID
$post = \verbb\socialposter\elements\Post::find()
    ->uid('xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx')
    ->one();
```
:::
:::
