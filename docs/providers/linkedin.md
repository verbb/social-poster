# Linked.in
Follow these steps to configure Linked.in for Social Poster.

## Step 1: Register a Linked.in App
1. Go to <a href="https://www.linkedin.com/developers/apps/new" target="_blank">Linked.in Developer Apps</a> and login to your account.
1. Click the **Create App** button and complete all the required fields.
1. Navigate to the **Products** section.
1. Click the **Select** button for the **Sign In with LinkedIn** product.
1. Navigate to the **Auth** section.
1. Click the edit icon for the **Authorized Redirect URLs** field.
1. Enter the value from the **Redirect URI** field in Social Poster.
1. Copy the **Client ID** from {name} and paste in the **Client ID** field in Social Poster.
1. Copy the **Client Secret** from {name} and paste in the **Client Secret** field in Social Poster.


## Company Pages
In order to post to company pages, there are a few requirements:

1. Ensure your Linked.in app is correctly linked and verified to the company page in your app **Settings** tab.
1. The Linked.in account used to authorise with Social Poster must be an admin of the company.
1. Have [Marketing Developer Platform](https://business.linkedin.com/marketing-solutions/case-studies/businessonline) permissions (see below).

### Marketing Developer Platform
To gain permissions for posting to company pages, you must sign up for the [Marketing Developer Platform](https://business.linkedin.com/marketing-solutions/case-studies/businessonline).

Go to the **Products** tab in your Linked.in app and click **Add more products**. Check the **Marketing Developer Platform** checkbox and follow the prompts to fill out your application. Please note that the approval process can take up to 20-25 days. Please be aware that LinkedIn will not approve every application submitted.

Once approved, verify you have the correct permissions via the **Auth** tab. Ensure you have `w_organization_social` and `r_organization_social` permissions. You should now be able to post to Linked.in company pages.

If you try to post to a company page without these permissions, you'll likely receive a permission error.

### Posting to both Company and Personal pages
If you want to be able to post to **both** company and personal pages, you'll need to include the following scopes in your request to authenticate with the Linked.in app. Be sure to also follow the above steps to ensure your app has the correct permissions.

If you haven't already done so, create a `social-poster.php` file in your `/config` directory. See the [config docs](docs:get-started/configuration) for more information.

Use the following for Linked.in:

```php
'linkedin' => [
    'clientId' => 'xxxxxxxxxxxx',
    'clientSecret' => 'xxxxxxxxxxxx',

    'scope' => [
        'w_organization_social',
        'r_organization_social',
    ],
],
```

Including these scopes tells Linked.in to authorise Social Poster to use these scopes. This is done automatically if you want to post to **just** the company page, but not if you want to use both. This should allow you to authorise against both possibilities.

