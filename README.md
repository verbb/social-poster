# Social Poster Plugin for Craft CMS

<img width="500" src="https://verbb.io/uploads/plugins/social-poster/social-poster-social-card.png?v=1">

Social Poster is a Craft CMS plugin for automatically posting entries to social media.

## Features
- Enable specific sections where the posting 'widget' appears.
- Setup defaults for each provider, including message and enabled.
- Have multiple accounts for multiple providers.
- Allows you to use your field content in your posts through Twig.
- Selectively post to different networks, or re-post on-demand.
- Provides events to write your own providers.

## Supports
- Facebook (Pages and Groups)
- Linked.in
- Twitter

### Facebook Compatibility
Please note there are some limitations when it comes to posting to Facebook, due to recent Facebook API restrictions.

#### Groups
In order to post to your Group, you'll be required to submit your Facebook App for review. From the Facebook docs:

> Use of this endpoint requires App Review. Apps that have already been approved to access this endpoint have until August 1, 2018, to resubmit for review or lose endpoint access.

[Graph API - Group](https://developers.facebook.com/docs/graph-api/reference/v3.2/group)

#### Pages
Posting to a Facebook Page does not require an app submission, and should work out of the box.

#### Profile
It is currently not possible to automatically post to your Facebook Wall/Timeline, due to Facebook removing the permission to do so. Even if you have a published app, you'll not be able to use this functionality any more.

> Due to the deprecation of the publish_actions permission, you can no longer use the API to publish user posts. Please use Sharing to enable people to post to Facebook from your app.

[Graph API - User](https://developers.facebook.com/docs/graph-api/reference/v3.2/user/feed#publish)

## Documentation

Visit the [Social Poster Plugin page](https://verbb.io/craft-plugins/social-poster) for all documentation, guides, pricing and developer resources.

## Support

Get in touch with us via the [Social Poster Support page](https://verbb.io/craft-plugins/social-poster/support) or by [creating a Github issue](https://github.com/verbb/social-poster/issues)

<h2></h2>

<a href="https://verbb.io" target="_blank">
  <img width="100" src="https://verbb.io/assets/img/verbb-pill.svg">
</a>
