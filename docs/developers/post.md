# Post

Whenever you're dealing with a post in your template, you're actually working with a `Post` object.

### Attributes

Attribute | Description
--- | ---
`id` | ID of the post.
`accountId` | The account ID this post was made from.
`ownerId` | The entry ID this post was made on.
`ownerSiteId` | The site ID this post was made on.
`owner` | [Entry](https://docs.craftcms.com/api/v3/craft-elements-entry.html) this post was made on.
`ownerType` | The class name of the element this post was made on.
`settings` | Serialized content that was used to send out this post.
`success` | Boolean whether the post was successfully sent to social media platforms.
`response` | A short response from the social media provider.
`data` | A full response from the social media provider.

## Methods

Method | Description
--- | ---
`getAccount()` | Returns the account this post was made from.
`getProvider()` | Returns the provider to post was  made from.
