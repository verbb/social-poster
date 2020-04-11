# Facebook

Follow these steps to configure Facebook for social poster:

## OAuth Configuration

1. Go to the [Facebook API Manager](https://developers.facebook.com/apps).
1. Click the “Add a New App” button to create a new Facebook application.
1. Once created, go to your application and set up the “Facebook Login” product.
1. Go to **Facebook API Manager → Your App → Facebook Login → Settings**, fill the “Valid OAuth redirect URIs” field with the Redirect URI found in **Craft Control Panel → Settings → Social Poster → Providers → Facebook**, and save.
1. Go to **Facebook API Manager → Your App → Settings → Basic** and copy the App ID and App Secret to **Craft Control Panel → Settings → Social Poster → Providers → Facebook**, and use them as client ID and client secret values.

Please note there are some limitations when it comes to posting to Facebook, due to Facebook API restrictions.

## Posting to Facebook Profile/Wall

It is currently not possible to automatically post to your Facebook Wall/Timeline, due to Facebook removing the permission to do so. Even if you have a published app, you'll not be able to use this functionality any more.

According to [Facebook API docs](https://developers.facebook.com/docs/graph-api/reference/v3.2/user/feed#publish):

```
As of April 24,2018, the `publish_actions` permission has been removed. Please see the [Breaking Changes Changelog](https://developers.facebook.com/docs/graph-api/changelog/breaking-changes#login-4-24) for more details. To provide a way for your app users to share content to Facebook, we encourage you to use our [Sharing products](https://developers.facebook.com/docs/sharing) instead.
```

## Posting to Facebook Group

In order to post to a Group, you'll be required to submit your Facebook App for review. 

From the Facebook docs:

```
Use of this endpoint requires App Review. Apps that have already been approved to access this endpoint have until August 1, 2018, to resubmit for review or lose endpoint access.
```

Please refer to our [Submit Facebook App for Review](#submit-facebook-app-for-review) section.

## Posting to Facebook Page

In order to post to a Page, you'll be required to submit your Facebook App for review. You'll need the `manage_pages` and `publish_pages` permissions.

Please refer to our [Submit Facebook App for Review](#submit-facebook-app-for-review) section.

## Submit Facebook App for Review

Facebook's API's have become much more restricted in recent years, and you'll almost certainly be required to submit your Facebook App for review. Credit to [@geoffreyvandamme](https://github.com/verbb/social-poster/issues/32) for outlining these steps.

1. First, create a user in Craft with permissions to access Social Poster, and to edit/publish entries in the desired section(s). Take note of the username/password of this account, as you'll be providing this to Facebook in your submission.
1. Take note of the permissions required for the posting type you've selected (Page or Group).
1. Turn your Facebook App to "Live" mode. There's a lightswitch control at the top of the page to toggle this.
1. Go to "App Review" → "Permission and Features" menu.
1. Find the permissions required as per the second step (`manage_pages`, `publish_pages`, etc). Click the "Request" button for each.
1. Click the "Continue the Request" button, and continue following the steps.
1. Fill in the verification details, by providing login credentials to your site, and instructions on how the posting functionality should work. You will also need to provide a screencast. See examples below.

#### Sample Detailed description

You're required to explain why you require these permissions to use Facebook's APIs. Be as descriptive as possible, and you can follow a similar structure as the below (but please change it to your scenario and client needs).

```
We have a Craft CMS website for a client we are working with. We want to share content created on their website on social media, specifically Facebook. When our client saves an entry, we want it to automatically be pushed to Facebook - with their approval of course. This allows our client to not have to post their content in multiple places at once, saving massive amounts of time and effort.

We have purchased a plugin for Craft CMS called "Social Poster" (see https://verbb.io/craft-plugins/social-poster) that facilitates this. 

Please refer to the attached screencast outlining the steps to connect and setup this plugin. The screencast shows posting to a Facebook Page successfully, but as the Facebook app is still in development mode, it will only appear to us.

Please login to our Craft CMS website, via:

https://yourdomain.com/admin/
Username: *******
Password: *******

Proceed to https://yourdomain.com/admin//entries/blog/5069-testing and on the right-hand side you'll see a widget to control these posts going to Facebook. Ensure that it is ticked as enabled, then hit the red "Save" button in the top-right of the page. The content of the entry you've edited should show on the Facebook Page successfully.

#### Screencast

It's a requirement to provide a screencast, with a step-by-step outline of how you want to use Facebook's APIs. Ensure your screencast outlines the following:

1. Logging into Craft
1. Showing the Facebook account in Social Poster.
1. Showing the Facebook provider in Social Poster. Its advisable your Facebook app is in development mode still, so that you can show posting functionality.
1. Disconnect and re-connect with Facebook to show the authorisation process. Its important to show the Facebook account you're authorising Social Poster to use to do the actual posting.
1. Navigate to an entry which has Social Poster enabled, so that the right-hand sidebar is shown. Either create a new entry, or edit an existing one.
1. Save the entry, ensuring you have posting to Facebook enabled.
1. Show the post, posted to your chosen page in Facebook. As the app is in development mode, only you can see the post - which is the reason you need these permissions, so that everyone can see them.

You need to wait up to 5 days for Facebook to review your app. You can keep your Facebook App in "Development" mode, but no posts you make will be publicly visible, only visible to you.

## Troubleshooting

### Error - Invalid Scopes

You may receive the following error when trying to connect to Facebook:

```
Invalid Scopes: publish_pages, publish_to_groups, manage_pages, user_posts, user_photos. This message is only shown to developers. Users of your app will ignore these permissions if present. Please read the documentation for valid permissions at: https://developers.facebook.com/docs/facebook-login/permissions
```

As per new Facebook platform product changes and policy updates:

```
Apps in public mode no longer allow their admins, developers, or testers to access permissions or features that normally require app review. This affects all apps built after May 1st, 2018, immediately. Apps built before then will not be affected until August 1st, 2018.
```

To fix, ensure your app is in development mode, not live mode.

### Error - Incorrect Redirect URL

If your redirect URL in your Facebook app looks similar to the following:

```
https://craftcms.com/index.php?p=actions%2Fsocial-poster%2Faccounts%2Fcallback
```

Facebook will raise an issue that this authorised redirect URI doesn't exactly match the one in Craft. This is due to the encoded characters by having the action path in a query string.

To fix, set `usePathInfo` to `true` in your `general.php` file, which will change the redirect URL in your provider settings, and use this to update your Facebook app.
