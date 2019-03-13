# Twitter

Follow these steps to configure Twitter for social poster:

## OAuth Configuration

### Step 1: Create a new app
1. Go to the [Twitter Application Manager](https://dev.twitter.com/apps).
1. Click “Create New App” to create a new Twitter application.
1. Fill all required fields.
1. Fill the “Callback URL” field with the Redirect URI found in **Craft Control Panel → Settings → Social Poster → Providers → Twitter**.
1. Agree to the terms and save the application.

### Step 2: Setup app permissions
1. First, you need to [contact Twitter to whitelist your app](https://support.twitter.com/forms/platform) to be able to request a user’s email.
1. Click “I need access to special permissions” and fill your application details.
1. In **Permissions Requested** ask for the “email” special permission.
1. Twitter will send you an email to confirm that you have email access (it usually takes less than 24 hours).
1. Now go back to the Twitter Application manager and click on the app that you've just created to edit it.
1. Under **Permissions → Access**, select “Read and write” (don’t choose the one that gives access to Direct Messages otherwise social login will fail).
1. Under **Permissions → Additional Permissions**, check the **Request email addresses from users** box (this will only be visible once Twitter has whitelisted your app).

### Step 3: OAuth settings in Craft
1. Twitter will provide you a consumer key and a consumer secret for your application, copy them to **Craft Control Panel → Settings → Social Poster → Providers → Twitter**, and use them as client ID and client secret values.

