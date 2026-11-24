<?php
namespace verbb\socialposter\controllers;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\models\Payload;

use Craft;
use craft\web\Controller;

use yii\web\Response;

class PostsController extends Controller
{
    // Public Methods
    // =========================================================================

    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $this->requireCpRequest();
        $this->requirePermission('socialPoster-posts');

        return true;
    }

    public function actionIndex(): Response
    {
        // Until an entry is posted, the setup checklist takes the place of the Posts index.
        if ($setup = SocialPoster::$plugin->getPosts()->getSetupSteps()) {
            return $this->renderTemplate('social-poster/posts/_setup', [
                'setup' => $setup,
            ]);
        }

        return $this->renderTemplate('social-poster/posts');
    }

    public function actionEdit(int $postId = null): Response
    {
        $post = SocialPoster::$plugin->getPosts()->getPostById($postId);

        return $this->renderTemplate('social-poster/posts/_edit', [
            'post' => $post,
        ]);
    }

    public function actionRepost(): ?Response
    {
        $this->requirePostRequest();

        $postId = $this->request->getParam('id');
        $post = SocialPoster::$plugin->getPosts()->getPostById($postId);

        if (!$post) {
            Craft::$app->getSession()->setError(Craft::t('social-poster', 'Couldn’t re-post.'));

            return null;
        }

        $account = $post->getAccount();
        $payload = new Payload($post->settings);
        $payload->element = $post->getOwner();

        if ($account && $account->sendPost($payload)->success) {
            Craft::$app->getSession()->setNotice(Craft::t('social-poster', 'Re-posted successfully.'));
        } else {
            Craft::$app->getSession()->setError(Craft::t('social-poster', 'Couldn’t re-post.'));
        }

        return $this->redirectToPostedUrl();
    }
}
