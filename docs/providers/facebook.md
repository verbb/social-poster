# Facebook
Follow these steps to configure Facebook for Social Poster.

:::tip
When posting to a Facebook Page, your Facebook App **does not** require review and approval by Facebook. However, to post to a Facebook Group, it **does** require review and approval by Facebook.
:::

## Step 1. Admin Access to Facebook Page or Facebook Group
In order to post to a Facebook Page or Facebook Group, you must be an Admin for the page/group you want to post to.

## Step 2: Register a Facebook App
1. Go to the <a href="https://developers.facebook.com/apps/" target="_blank">Meta for Developers</a> page.
1. Click the **Create App** button.
1. Select **Other** and **Business** as the app type, and fill in the rest of the details.
1. Once created, in the left-hand sidebar, click the **Add Product** button.
1. Under **Facebook Login** click the **Set Up** button.
1. Select **Web** as the type and your website address into **Site URL**, and click the **Save** button.
1. Navigate to the **Facebook Login** section in the left-hand siderbar, click **Settings**.
1. For the **Valid OAuth Redirect URIs** setting, enter the value from the **Redirect URI** field in Social Poster.
1. Click the **Save Changes** button.
1. Navigate to **Settings** → **Basic** item in the left-hand sidebar.
1. Enter your domain name to the **App Domains** field.
1. Enter your **Privacy Policy URL**, **Terms of Service URL** and **Site URL**.
1. Click the **Save Changes** button.
1. Change the **App Mode** to **Live**.
1. Copy the **App ID** from Facebook and paste in the **Client ID** field in Social Poster.
1. Copy the **App Secret** from Facebook and paste in the **Client Secret** field in Social Poster.
1. Save the Social Poster account, ready to connect.

## Step 3: Connect to Facebook
1. In the Social Poster account settings, click the **Connect** button and login to Facebook.
1. Ensure you pick either the Facebook Group or Facebook Page you have admin access to.

## Step 4: Select your Facebook Page or Facebook Group
1. Select either a **Facebook Page** or a **Facebook Group** that you'd like connected to.
1. Click the **Save** button for the account.

## Step 5: Facebook Group
1. If you're using a Facebook Page, you can skip this step.
1. Ensure you install the Facebook app to your Facebook Group as per [instructions](https://www.facebook.com/help/261149227954100). Note that in order to install your app, it will need to be reviewed and published by Facebook.

## Troubleshooting

### No Pages appear in the dropdown
Some users are unable to choose any Pages from the dropdown for the settings of an account, once their app has been authorized. This is due to how your Facebook app has been setup in relation to the Pages it has access to.

If you're finding this is the case for you, ensure that you provide the `business_management` additional scope. You'll need to disconnect and reconnect your account. You can do this by adding the following to your [configuration file](docs:get-started/configuration).


```php
<?php

return [
    '*' => [
        // ...
        'accounts' => [
            'facebook' => [
                // ...
                'scopes' => [
                    'business_management',
                ],
            ],
        ],
    ]
];
```

## Limitations
Please note there are some limitations when it comes to posting to Facebook, due to Facebook API restrictions.

### Posting to Facebook Profile
It is not possible to post to your Facebook Profile, due to Facebook removing the permission to do so. Even if you have a published app, you'll not be able to use this functionality anymore.

According to [Facebook API docs](https://developers.facebook.com/docs/graph-api/changelog/non-versioned-changes/apr-24-2018#login-4-24):

```
As of April 24,2018, the `publish_actions` permission has been removed. Please see the [Breaking Changes Changelog](https://developers.facebook.com/docs/graph-api/changelog/breaking-changes#login-4-24) for more details. To provide a way for your app users to share content to Facebook, we encourage you to use our [Sharing products](https://developers.facebook.com/docs/sharing) instead.
```

### Posting to Facebook Group
In order to post to a Group, you'll be required to submit your Facebook App for review. 

From the Facebook docs:

```
Use of this endpoint requires App Review. Apps that have already been approved to access this endpoint have until August 1, 2018, to resubmit for review or lose endpoint access.
```

Please refer to our [Submit Facebook App for Review](#submit-facebook-app-for-review) section.

### Submit Facebook App for Review
Facebook's API's have become much more restricted in recent years, and in some cases you'll be required to submit your Facebook App for review. Credit to [@geoffreyvandamme](https://github.com/verbb/social-poster/issues/32) for outlining these steps.

1. First, create a user in Craft with permissions to access Social Poster, and to edit/publish entries in the desired section(s). Take note of the username/password of this account, as you'll be providing this to Facebook in your submission.
1. Take note of the permissions required for the posting type you've selected (Page or Group).
1. Turn your Facebook App to "Development" mode. There's a lightswitch control at the top of the page to toggle this.
1. Go to "App Review" → "Permission and Features" menu.
1. Find the permissions required as per the second step (`pages_manage_posts`, `pages_read_user_content`, etc). Click the "Request" button for each.
1. Click the "Continue the Request" button, and continue following the steps.
1. Fill in the verification details, by providing login credentials to your site, and instructions on how the posting functionality should work. You will also need to provide a screencast. See examples below.
1. Once you have received approval for your app to use additional permissions, turn your Facebook App to "Live" mode.
1. Connect Social Poster to Facebook.

#### Sample Description
You're required to explain why you require these permissions to use Facebook's APIs. Be as descriptive as possible, and you can follow a similar structure as the below (but please change it to your scenario and client needs).

```
We have a Craft CMS website for a client we are working with. We want to share content created on their website on social media, specifically Facebook. When our client saves an entry, we want it to automatically be pushed to Facebook - with their approval of course. This allows our client to not have to post their content in multiple places at once, saving massive amounts of time and effort.

We have purchased a plugin for Craft CMS called "Social Poster" (see https://verbb.io/craft-plugins/social-poster) that facilitates this. 

Please refer to the attached screencast outlining the steps to connect and setup this plugin. The screencast shows posting to a Facebook Page successfully, but as the Facebook app is still in development mode, it will only appear to us.

Please login to our Craft CMS website, via:

https://craft-site.test/admin/
Username: *******
Password: *******

Proceed to https://craft-site.test/admin/entries/an-example-entry and on the right-hand side you'll see a widget to control these posts going to Facebook. Ensure that it is ticked as enabled, then hit the red "Save" button in the top-right of the page. The content of the entry you've edited should show on the Facebook Page successfully.
```

#### Screencast Required Steps
It's a requirement to provide a screencast, with a step-by-step outline of how you want to use Facebook's APIs. This **must** be a recording with voice included, it cannot be video-only. Ensure your screencast outlines the following:

1. Logging into Craft
1. Showing the Facebook account in Social Poster.
1. Showing the Facebook provider in Social Poster. Its advisable your Facebook app is in development mode still, so that you can show posting functionality.
1. Disconnect and re-connect with Facebook to show the authorisation process. It's important to show the Facebook account you're authorising Social Poster to use to do the actual posting.
1. Navigate to an entry which has Social Poster enabled, so that the right-hand sidebar is shown. Either create a new entry, or edit an existing one.
1. Save the entry, ensuring you have posting to Facebook enabled.
1. Show the post, posted to your chosen page in Facebook. As the app is in development mode, only you can see the post - which is the reason you need these permissions, so that everyone can see them.

You need to wait up to 5 days for Facebook to review your app. You can keep your Facebook App in "Development" mode, but no posts you make will be publicly visible, only visible to you.
