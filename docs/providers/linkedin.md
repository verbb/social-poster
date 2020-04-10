# Linked.in

Follow these steps to configure Linked.in for social poster:

## OAuth Configuration

### Step 1: Create a new app
1. Go to the [Linked.in Developer Apps](https://www.linkedin.com/developers/apps).
1. Click “Create App” to create a new Linked.in application.
1. Fill all required fields and create the app.
1. Once created, click on the "Auth" tab, and add the Redirect URI found in **Craft Control Panel → Settings → Social Poster → Providers → Linked.in**.

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
