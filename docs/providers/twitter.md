# Twitter

Follow these steps to configure Twitter for social poster:

## OAuth Configuration

### Step 1. Navigate to the Twitter Provider
1. Navigate to **Social Poster** → **Providers** → **Twitter** in your Craft install.

### Step 2. Connect to the Twitter API
1. Go to <a href="https://developer.twitter.com/en/apps" target="_blank">Twitter Developer Portal</a> and login to your account.
1. If you need to apply for a developer account, do so. Fill out the details as they apply to your organisation.
1. In the left-hand menu, click **Projects & Apps**.
1. Under the **Standalone Apps** heading click the **+ Create App** button.
1. Provide your **App Name** and click **Next**.
1. Copy the **API Key** from Twitter and paste in the **Client ID** field below.
1. Copy the **API Key Secret** from Twitter and paste in the **Client Secret** field below.
1. Click on the **App Settings** button.
1. Under the **App Permissions** heading click the **Edit** button.
1. Select **Read and Write** and click **Save**.
1. Under the **Authentication Settings** heading click the **Edit** button.
1. Enable **Enable 3-legged OAuth** and **Request email address from users**.
1. In the **Callback URLs** field, enter the value from the **Redirect URI** field in Social Poster.
1. Fill out the rest of the form's URLs as required and click **Save**.

## Troubleshooting

### Error - Incorrect Redirect URL

If your redirect URL in your Twitter app looks similar to the following:

```
https://craft-site.test/index.php?p=actions%2Fsocial-poster%2Faccounts%2Fcallback
```

Twitter will raise an issue that this authorised redirect URI doesn't exactly match the one in Craft. This is due to the encoded characters by having the action path in a query string.

To fix, set [usePathInfo](https://docs.craftcms.com/v3/config/config-settings.html#usepathinfo) to `true` in your `general.php` file, which will change the redirect URL in your provider settings, and use this to update your Twitter app.
