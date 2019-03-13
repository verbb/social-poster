# Facebook

Follow these steps to configure Facebook for social poster:

## OAuth Configuration

1. Go to the [Facebook API Manager](https://developers.facebook.com/apps).
1. Click the “Add a New App” button to create a new Facebook application.
1. Once created, go to your application and set up the “Facebook Login” product.
1. Go to **Facebook API Manager → Your App → Facebook Login → Settings**, fill the “Valid OAuth redirect URIs” field with the Redirect URI found in **Craft Control Panel → Settings → Social Poster → Providers → Facebook**, and save.
1. Go to **Facebook API Manager → Your App → Settings → Basic** and copy the App ID and App Secret to **Craft Control Panel → Settings → Social Poster → Providers → Facebook**, and use them as client ID and client secret values.

## Facebook Compatibility
Please note there are some limitations when it comes to posting to Facebook, due to recent Facebook API restrictions.

#### Groups
In order to post to your Group, you'll be required to submit your Facebook App for review. From the Facebook docs:

```
Use of this endpoint requires App Review. Apps that have already been approved to access this endpoint have until August 1, 2018, to resubmit for review or lose endpoint access.
```

[Graph API - Group](https://developers.facebook.com/docs/graph-api/reference/v3.2/group)

#### Pages
Posting to a Facebook Page does not require an app submission, and should work out of the box.

#### Profile
It is currently not possible to automatically post to your Facebook Wall/Timeline, due to Facebook removing the permission to do so. Even if you have a published app, you'll not be able to use this functionality any more.

```
Due to the deprecation of the publish_actions permission, you can no longer use the API to publish user posts. Please use Sharing to enable people to post to Facebook from your app.
```

[Graph API - User](https://developers.facebook.com/docs/graph-api/reference/v3.2/user/feed#publish)

## Troubleshooting

### Error - Invalid Scopes

You may receive the following error when trying to connect to Facebook:

```
Invalid Scopes: publish_pages, user_posts, manage_pages, user_photos. This message is only shown to developers. Users of your app will ignore these permissions if present. Please read the documentation for valid permissions at: https://developers.facebook.com/docs/facebook-login/permissions
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
