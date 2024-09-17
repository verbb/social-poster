# Instagram
Follow these steps to configure Instagram for Social Poster.

:::tip
Your Facebook/Instagram App **does not** require review and approval by Facebook/Instagram to use Social Poster.
:::

:::warning
In order to post to Instagram, you must ensure the following:

- Your Instagram account is set to "Business" and not "Creator".
- Your Instagram account is linked to a Facebook page.
- You configure the **Image Field for Post** setting for an account.
- Posts have an asset in the nominated image field. This asset **must** have a public URL, and cannot be a locally served-asset in order for Instagram to retrieve it.
:::

## Step 1. Create an Instagram Business Account
If you already have a personal account on Instagram, you can easily convert it to a business account. An Instagram Business account is required to post.

[How to Set Up a Business Account on Instagram](https://help.instagram.com/502981923235522)

## Step 2: Connect a Facebook Page to your Instagram Business Account
Despite not posting to your Facebook page, you must link your Instagram Business Account with a Facebook page.

[How to Connect a Facebook Page to Your Instagram Business Account](https://help.instagram.com/399237934150902)

## Step 3: Register a Facebook App
1. Go to the <a href="https://developers.facebook.com/apps/" target="_blank">Meta for Developers</a> page.
1. Click the **Create App** button.
1. Select **I don’t want to connect a business portfolio yet.** and click the **Next** button.
1. Select **Other** and click the **Next** button.
1. Select **Business** and click the **Next** button.
1. Fill in the **App Name** and **App Contact Email**. The **Business Portfolio** is optional.

### Step 4: Setup Instagram Graph API
1. Once created, in the left-hand sidebar, click the **Add Product** button.
1. Under **Instagram Graph API** click the **Set Up** button.

### Step 5: Setup Facebook Login for Business
1. In the left-hand sidebar, click the **Add Product** button.
1. Under **Facebook Login for Business** click the **Set Up** button.
1. Navigate to the **Facebook Login for Business** section in the left-hand sidebar, click **Settings**.
1. For the **Valid OAuth Redirect URIs** setting, enter the value from the **Redirect URI** field in Social Feeds.
1. Click the **Save Changes** button.
1. Navigate to **App Settings** → **Basic** item in the left-hand sidebar.
1. Enter your domain name to the **App Domains** field.
1. Enter your **Privacy Policy URL** and **Terms of Service URL**.
1. Click the **Save Changes** button.
1. Copy the **App ID** from Facebook and paste in the **Client ID** field in Social Poster.
1. Copy the **App Secret** from Facebook and paste in the **Client Secret** field in Social Poster.
1. Save the Social Poster account, ready to connect.

## Step 6: Connect to Instagram
1. In the Social Poster account settings, click the **Connect** button and login to Instagram.

## Step 7: Select your Facebook Page
1. Select the **Facebook Page** that has access to your Instagram Business Account.
1. Click the **Save** button for the account.
