# LinkedIn
Follow these steps to configure LinkedIn for Social Poster.

## Step 1: Register a LinkedIn App
1. Go to <a href="https://www.linkedin.com/developers/apps/new" target="_blank">LinkedIn Developer Apps</a> and login to your account.
1. Click the **Create App** button and complete all the required fields.
1. Navigate to the **Products** section.
1. If you want to post to a personal LinkedIn Page:
    1. Click the **Request Access** button for the **Sign In with LinkedIn using OpenID Connect** product.
    1. Click the **Request Access** button for the **Share on LinkedIn** product.
1. If you want to post to a LinkedIn Company Page:
    1. Click the **Request Access** button for the **Community Management API** product.
        - You will be required to authenticate with your Company, and submit an access request form.
1. Navigate to the **Auth** section.
1. Click the edit icon for the **Authorized Redirect URLs** field.
1. Enter the value from the **Redirect URI** field in Social Poster.
1. Copy the **Client ID** from LinkedIn and paste in the **Client ID** field in Social Poster.
1. Copy the **Client Secret** from LinkedIn and paste in the **Client Secret** field in Social Poster.


## Company Pages vs Personal Pages
If you want to post to **only** personal pages, you will only require the **Share on LinkedIn** product with your LinkedIn app. If you want to post to a Company page, or both a Personal _and_ Company page, you'll need to apply for the **Community Management API** product.

Once approved, verify you have the correct permissions via the **Auth** tab. Ensure you have `w_organization_social` and `r_organization_social` permissions. You should now be able to post to LinkedIn company pages.

For posting to Personal pages, only the `w_member_social` permission is required.
