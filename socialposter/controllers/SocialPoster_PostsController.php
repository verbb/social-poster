<?php
namespace Craft;

class SocialPoster_PostsController extends BaseController
{
    // Public Methods
    // =========================================================================

    //
    // Control Panel
    //

    public function actionIndex()
    {
        craft()->runController('socialPoster/plugin/checkRequirements');

        $posts = craft()->socialPoster_posts->getAll();
        $providers = craft()->socialPoster_accounts->getAccountProviders(false);

        if ($posts) {
            $posts = array_reverse($posts);
        }
        
        $this->renderTemplate('socialposter/posts', array(
            'providers' => $providers,
            'posts' => $posts,
        ));
    }

    public function actionEdit(array $variables = array())
    {
        craft()->runController('socialPoster/plugin/checkRequirements');
        
        $post = craft()->socialPoster_posts->getById($variables['postId']);
        $provider = craft()->socialPoster_accounts->getAccountProvider($post->handle);

        $this->renderTemplate('socialposter/posts/_edit', array(
            'post' => $post,
            'provider' => $provider,
        ));
    }

    public function actionRepost()
    {
        $this->requirePostRequest();

        $postId = craft()->request->getPost('id');
        $post = craft()->socialPoster_posts->getById($postId);

        $entry = craft()->entries->getEntryById($post->elementId);
        $provider = $post->providerSettings;
        $providerHandle = $post->handle;
        $picture = null;

        if ($result = craft()->socialPoster->sendSocialPost($entry, $provider, $providerHandle, $picture)) {
            craft()->userSession->setNotice(Craft::t('Re-posted successfully.'));
        } else {
            craft()->userSession->setError($result);
        }

        $this->redirectToPostedUrl();
    }

}

