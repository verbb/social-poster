# Finding Posts

Each time Social Poster sends an entry to an account, it saves a post recording which account it went to, when it was sent and whether it worked. `craft.socialPoster.posts()` returns a [post query](docs:getting-elements/post-queries) for those records, so your templates can show where an entry has been shared, or list the posts that failed. Post Queries lists every parameter you can add to the query.

## Showing Where an Entry Has Been Shared

This example belongs in an entry's template, such as below the article. It lists the accounts the entry was sent to, newest first, and skips any attempts that failed.

```twig
{% set posts = craft.socialPoster.posts()
    .ownerId(entry.id)
    .all() %}

{% set sentPosts = posts | filter(post => post.success) %}

{% if sentPosts | length %}
    <h2>Shared On</h2>

    <ul>
        {% for post in sentPosts %}
            {% set account = post.getAccount() %}

            <li>{{ account ? account.name : 'A removed account' }}, {{ post.dateCreated | date('j F Y') }}</li>
        {% endfor %}
    </ul>
{% else %}
    <p>This article hasn't been shared yet.</p>
{% endif %}
```

An entry sent to a Facebook page and a LinkedIn profile lists both accounts by the names they have under **Social Poster** → **Accounts**, with the date each post was sent. An entry that hasn't been sent anywhere, or where every attempt failed, shows “This article hasn't been shared yet.”

`.ownerId(entry.id)` finds the posts sent for this entry. Posts come back newest first, as an array of [Post](docs:reference/post) objects. There's no query parameter for whether a post worked, so the example fetches them all and keeps the successful ones with Twig's `filter`. `post.getAccount()` returns the [Account](docs:reference/account) the post went to, or `null` if that account has since been deleted, which is why the example checks it before using its name.

## Listing Posts That Failed

When an account's connection expires or a provider rejects a post, the post is saved with `success` set to `false`, and its `response` holds the error. A page listing these helps your team spot problems without opening each entry in the control panel.

Posts include provider error details, so put this in a template only your team can reach. The `{% requireLogin %}` tag sends anyone who isn't signed in to the login page. This example lists the last 50 posts that failed.

```twig
{% requireLogin %}

{% set failedPosts = craft.socialPoster.posts()
    .limit(50)
    .all() | filter(post => not post.success) %}

<h1>Posts That Didn't Send</h1>

<table>
    <thead>
        <tr>
            <th>Entry</th>
            <th>Account</th>
            <th>Sent</th>
            <th>Error</th>
        </tr>
    </thead>
    <tbody>
        {% for post in failedPosts %}
            {% set account = post.getAccount() %}

            <tr>
                <td>{{ post.owner ? post.owner.title : 'A deleted entry' }}</td>
                <td>{{ account ? account.name : 'A removed account' }}</td>
                <td>{{ post.dateCreated | date('j F Y, g:i a') }}</td>
                <td>{{ post.response.reasonPhrase ?? 'No details' }}</td>
            </tr>
        {% else %}
            <tr>
                <td colspan="4">The last 50 posts were all sent.</td>
            </tr>
        {% endfor %}
    </tbody>
</table>
```

Each row shows the entry, the account and when the post was attempted, with the provider's reason, such as “Unauthorized” for an expired connection. `.limit(50)` applies before the filter, so the table checks the 50 most recent posts rather than finding 50 failures. To look further back, raise the limit, or narrow the query to a period with [`dateCreated`](docs:getting-elements/post-queries#datecreated) or to one account with [`accountId`](docs:getting-elements/post-queries#accountid).

`post.owner` is the entry the post was sent for, or `null` if the entry has been deleted. To link to the entry in the control panel, use `post.owner.cpEditUrl`.
