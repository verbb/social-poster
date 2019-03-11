<?php
namespace verbb\socialposter\controllers;

use verbb\socialposter\SocialPoster;

use Craft;
use craft\web\Controller;

class PostsController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionIndex()
    {
        // $request = Craft::$app->getRequest();

        // $posts = craft()->socialPoster_posts->getAll();
        // $providers = craft()->socialPoster_accounts->getAccountProviders(false);

        // if ($posts) {
        //     $posts = array_reverse($posts);
        // }

        // $element = new \verbb\socialposter\elements\Post();
        // $element->ownerId = 3402;
        // $element->ownerSiteId = 1;
        // $element->ownerType = 'craft\elements\Entry';
        // $element->accountId = 2;
        // $element->settings = [];
        // $element->success = [];
        // $element->response = [];
        // $element->data = [];

        // Craft::$app->getElements()->saveElement($element);

        return $this->renderTemplate('social-poster/posts', [
        //     'providers' => $providers,
        //     'posts' => $posts,
        ]);
    }

    public function actionEdit(int $postId = null)
    {
        $request = Craft::$app->getRequest();

        $post = SocialPoster::$plugin->getPosts()->getPostById($postId);

        return $this->renderTemplate('social-poster/posts/_edit', [
            'post' => $post,
        ]);
    }

    public function actionRepost()
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();

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

        return $this->redirectToPostedUrl();
    }

}

