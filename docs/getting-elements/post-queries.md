# Post Queries

You can fetch posts in your templates or PHP post using **post queries**.

:::code
```twig Twig
{# Create a new post query #}
{% set myQuery = craft.socialPoster.posts() %}
```

```php PHP
// Create a new post query
$myQuery = \verbb\socialposter\elements\Post::find();
```
:::

Once you’ve created a post query, you can set parameters on it to narrow down the results, and then execute it by calling `.all()`. An array of [Post](docs:developers/post) objects will be returned.

:::tip
See Introduction to [Element Queries](https://docs.craftcms.com/v3/dev/element-queries/) in the Craft docs to learn about how element queries work.
:::

## Example

We can display posts for a given entry by doing the following:

1. Create a post query with `craft.socialPoster.posts()`.
2. Set the [ownerId](#ownerId) and [limit](#limit) parameters on it.
3. Fetch all posts with `.all()` and output.
4. Loop through the posts using a [for](https://twig.symfony.com/doc/2.x/tags/for.html) tag to output the contents.

```twig
{# Create a posts query with the 'ownerId' and 'limit' parameters #}
{% set postsQuery = craft.socialPoster.posts()
    .ownerId(entry.id)
    .limit(10) %}

{# Fetch the Posts #}
{% set posts = postsQuery.all() %}

{# Display their contents #}
{% for post in posts %}
    <p>{{ post.id }}</p>
{% endfor %}
```

## Parameters

Post queries support the following parameters:

<!-- BEGIN PARAMS -->

### `after`

Narrows the query results to only posts that were posted on or after a certain date.

Possible values include:

| Value | Fetches posts…
| - | -
| `'2018-04-01'` | that were posted after 2018-04-01.
| a [DateTime](http://php.net/class.datetime) object | that were posted after the date represented by the object.

::: code
```twig
{# Fetch posts posted this month #}
{% set firstDayOfMonth = date('first day of this month') %}

{% set posts = craft.socialPoster.posts()
    .after(firstDayOfMonth)
    .all() %}
```

```php
// Fetch posts posted this month
$firstDayOfMonth = new \DateTime('first day of this month');

$posts = \verbb\socialposter\elements\Post::find()
    ->after($firstDayOfMonth)
    ->all();
```
:::



### `anyStatus`

Clears out the [status()](https://docs.craftcms.com/api/v3/craft-elements-db-elementquery.html#method-status) and [enabledForSite()](https://docs.craftcms.com/api/v3/craft-elements-db-elementquery.html#method-enabledforsite) parameters.

::: code
```twig
{# Fetch all posts, regardless of status #}
{% set posts = craft.socialPoster.posts()
    .anyStatus()
    .all() %}
```

```php
// Fetch all posts, regardless of status
$posts = \verbb\socialposter\elements\Post::find()
    ->anyStatus()
    ->all();
```
:::



### `asArray`

Causes the query to return matching posts as arrays of data, rather than [Post](docs:developers/post) objects.

::: code
```twig
{# Fetch posts as arrays #}
{% set posts = craft.socialPoster.posts()
    .asArray()
    .all() %}
```

```php
// Fetch posts as arrays
$posts = \verbb\socialposter\elements\Post::find()
    ->asArray()
    ->all();
```
:::



### `before`

Narrows the query results to only posts that were posted before a certain date.

Possible values include:

| Value | Fetches posts…
| - | -
| `'2018-04-01'` | that were posted before 2018-04-01.
| a [DateTime](http://php.net/class.datetime) object | that were posted before the date represented by the object.

::: code
```twig
{# Fetch posts posted before this month #}
{% set firstDayOfMonth = date('first day of this month') %}

{% set posts = craft.socialPoster.posts()
    .before(firstDayOfMonth)
    .all() %}
```

```php
// Fetch posts posted before this month
$firstDayOfMonth = new \DateTime('first day of this month');

$posts = \verbb\socialposter\elements\Post::find()
    ->before($firstDayOfMonth)
    ->all();
```
:::



### `dateCreated`

Narrows the query results based on the posts’ creation dates.

Possible values include:

| Value | Fetches posts…
| - | -
| `'>= 2018-04-01'` | that were created on or after 2018-04-01.
| `'< 2018-05-01'` | that were created before 2018-05-01
| `['and', '>= 2018-04-04', '< 2018-05-01']` | that were created between 2018-04-01 and 2018-05-01.

::: code
```twig
{# Fetch posts created last month #}
{% set start = date('first day of last month') | atom %}
{% set end = date('first day of this month') | atom %}

{% set posts = craft.socialPoster.posts()
    .dateCreated(['and', ">= #{start}", "< #{end}"])
    .all() %}
```

```php
// Fetch posts created last month
$start = new \DateTime('first day of next month')->format(\DateTime::ATOM);
$end = new \DateTime('first day of this month')->format(\DateTime::ATOM);

$posts = \verbb\socialposter\elements\Post::find()
    ->dateCreated(['and', ">= {$start}", "< {$end}"])
    ->all();
```
:::



### `dateUpdated`

Narrows the query results based on the posts’ last-updated dates.

Possible values include:

| Value | Fetches posts…
| - | -
| `'>= 2018-04-01'` | that were updated on or after 2018-04-01.
| `'< 2018-05-01'` | that were updated before 2018-05-01
| `['and', '>= 2018-04-04', '< 2018-05-01']` | that were updated between 2018-04-01 and 2018-05-01.

::: code
```twig
{# Fetch posts updated in the last week #}
{% set lastWeek = date('1 week ago')|atom %}

{% set posts = craft.socialPoster.posts()
    .dateUpdated(">= #{lastWeek}")
    .all() %}
```

```php
// Fetch posts updated in the last week
$lastWeek = new \DateTime('1 week ago')->format(\DateTime::ATOM);

$posts = \verbb\socialposter\elements\Post::find()
    ->dateUpdated(">= {$lastWeek}")
    ->all();
```
:::



### `fixedOrder`

Causes the query results to be returned in the order specified by [id](#id).

::: code
```twig
{# Fetch posts in a specific order #}
{% set posts = craft.socialPoster.posts()
    .id([1, 2, 3, 4, 5])
    .fixedOrder()
    .all() %}
```

```php
// Fetch posts in a specific order
$posts = \verbb\socialposter\elements\Post::find()
    ->id([1, 2, 3, 4, 5])
    ->fixedOrder()
    ->all();
```
:::



### `id`

Narrows the query results based on the posts’ IDs.

Possible values include:

| Value | Fetches posts…
| - | -
| `1` | with an ID of 1.
| `'not 1'` | not with an ID of 1.
| `[1, 2]` | with an ID of 1 or 2.
| `['not', 1, 2]` | not with an ID of 1 or 2.

::: code
```twig
{# Fetch the post by its ID #}
{% set post = craft.socialPoster.posts()
    .id(1)
    .one() %}
```

```php
// Fetch the post by its ID
$post = \verbb\socialposter\elements\Post::find()
    ->id(1)
    ->one();
```
:::

::: tip
This can be combined with [fixedOrder](#fixedorder) if you want the results to be returned in a specific order.
:::



### `inReverse`

Causes the query results to be returned in reverse order.

::: code
```twig
{# Fetch posts in reverse #}
{% set posts = craft.socialPoster.posts()
    .inReverse()
    .all() %}
```

```php
// Fetch posts in reverse
$posts = \verbb\socialposter\elements\Post::find()
    ->inReverse()
    ->all();
```
:::



### `limit`

Determines the number of posts that should be returned.

::: code
```twig
{# Fetch up to 10 posts  #}
{% set posts = craft.socialPoster.posts()
    .limit(10)
    .all() %}
```

```php
// Fetch up to 10 posts
$posts = \verbb\socialposter\elements\Post::find()
    ->limit(10)
    ->all();
```
:::



### `offset`

Determines how many posts should be skipped in the results.

::: code
```twig
{# Fetch all posts except for the first 3 #}
{% set posts = craft.socialPoster.posts()
    .offset(3)
    .all() %}
```

```php
// Fetch all posts except for the first 3
$posts = \verbb\socialposter\elements\Post::find()
    ->offset(3)
    ->all();
```
:::



### `orderBy`

Determines the order that the posts should be returned in.

::: code
```twig
{# Fetch all posts in order of date created #}
{% set posts = craft.socialPoster.posts()
    .orderBy('elements.dateCreated asc')
    .all() %}
```

```php
// Fetch all posts in order of date created
$posts = \verbb\socialposter\elements\Post::find()
    ->orderBy('elements.dateCreated asc')
    ->all();
```
:::



### `owner`

Sets the [ownerId](#ownerid) and [siteId](#siteid) parameters based on a given element.

::: code
```twig
{# Fetch posts created for this entry #}
{% set posts = craft.socialPoster.posts()
    .owner(myEntry)
    .all() %}
```

```php
// Fetch posts created for this entry
$posts = \verbb\socialposter\elements\Post::find()
    ->owner($myEntry)
    ->all();
```
:::



### `ownerId`

Narrows the query results based on the owner element of the posts, per the owners’ IDs.

Possible values include:

| Value | Fetches posts…
| - | -
| `1` | created for an element with an ID of 1.
| `'not 1'` | not created for an element with an ID of 1.
| `[1, 2]` | created for an element with an ID of 1 or 2.
| `['not', 1, 2]` | not created for an element with an ID of 1 or 2.

::: code
```twig
{# Fetch posts created for an element with an ID of 1 #}
{% set posts = craft.socialPoster.posts()
    .ownerId(1)
    .all() %}
```

```php
// Fetch posts created for an element with an ID of 1
$posts = \verbb\socialposter\elements\Post::find()
    ->ownerId(1)
    ->all();
```
:::



### `ownerSite`

Narrows the query results based on the site the owner element was saved for.

Possible values include:

| Value | Fetches posts…
| - | -
| `'foo'` | created for an element in a site with a handle of `foo`.
| `a [Site](https://docs.craftcms.com/api/v3/craft-models-site.html)` object | created for an element in the site represented by the object.

::: code
```twig
{# Fetch posts created for an element with an ID of 1, for a site with a handle of 'foo' #}
{% set posts = craft.socialPoster.posts()
    .ownerId(1)
    .ownerSite('foo')
    .all() %}
```

```php
// Fetch posts created for an element with an ID of 1, for a site with a handle of 'foo'
$posts = \verbb\socialposter\elements\Post::find()
    ->ownerId(1)
    .ownerSite('foo')
    ->all();
```
:::



### `ownerSiteId`

Narrows the query results based on the site the owner element was saved for, per the site’s ID.

Possible values include:

| Value | Fetches posts…
| - | -
| `1` | created for an element in a site with an ID of 1.
| `':empty:'` | created in a field that isn’t set to manage blocks on a per-site basis.

::: code
```twig
{# Fetch posts created for an element with an ID of 1, for a site with an ID of 2 #}
{% set posts = craft.socialPoster.posts()
    .ownerId(1)
    .ownerSiteId(2)
    .all() %}
```

```php
// Fetch posts created for an element with an ID of 1, for a site with an ID of 2
$posts = \verbb\socialposter\elements\Post::find()
    ->ownerId(1)
    .ownerSiteId(2)
    ->all();
```
:::



### `status`

Narrows the query results based on the posts’ statuses.

Possible values include:

| Value | Fetches posts…
| - | -
| `'live'` _(default)_ | that are live.
| `'pending'` | that are pending (enabled with a Post Date in the future).
| `'expired'` | that are expired (enabled with an Expiry Date in the past).
| `'disabled'` | that are disabled.
| `['live', 'pending']` | that are live or pending.

::: code
```twig
{# Fetch disabled posts #}
{% set posts = craft.socialPoster.posts()
    .status('disabled')
    .all() %}
```

```php
// Fetch disabled posts
$posts = \verbb\socialposter\elements\Post::find()
    ->status('disabled')
    ->all();
```
:::



### `uid`

Narrows the query results based on the posts’ UIDs.

::: code
```twig
{# Fetch the post by its UID #}
{% set post = craft.socialPoster.posts()
    .uid('xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx')
    .one() %}
```

```php
// Fetch the post by its UID
$post = \verbb\socialposter\elements\Post::find()
    ->uid('xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx')
    ->one();
```
:::


<!-- END PARAMS -->
