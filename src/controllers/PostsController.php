<?php
namespace verbb\socialposter\controllers;

use verbb\socialposter\SocialPoster;

use Craft;
use craft\web\Controller;

use yii\web\Response;

class PostsController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionIndex(): Response
    {
        return $this->renderTemplate('social-poster/posts');
    }

    public function actionEdit(int $postId = null): Response
    {
        $request = Craft::$app->getRequest();

        $post = SocialPoster::$plugin->getPosts()->getPostById($postId);

        return $this->renderTemplate('social-poster/posts/_edit', [
            'post' => $post,
        ]);
    }

    public function actionRepost(): Response
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();

        $postId = $request->getParam('id');
        $post = SocialPoster::$plugin->getPosts()->getPostById($postId);
        $account = $post->getAccount();
        $payload = $post->settings;

        if ($postResult = $account->provider->sendPost($account, $payload)) {
            Craft::$app->getSession()->setNotice(Craft::t('social-poster', 'Re-posted successfully.'));
        } else {
            Craft::$app->getSession()->setError(Craft::t('social-poster', 'Couldn’t re-post.'));
        }

        return $this->redirectToPostedUrl();
    }
}
