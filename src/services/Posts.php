<?php
namespace verbb\socialposter\services;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\base\OAuthAccount;
use verbb\socialposter\elements\Post;

use Craft;
use craft\base\Component;
use craft\db\Query;

class Posts extends Component
{
    // Public Methods
    // =========================================================================

    public function getPostById(int $id, $siteId = null): ?Post
    {
        return Craft::$app->getElements()->getElementById($id, Post::class, $siteId);
    }

    /**
     * Returns what the Posts page's setup checklist needs, or null once an entry has been posted.
     */
    public function getSetupSteps(): ?array
    {
        // Read the table rather than the element query, so a trashed post still counts and deleting a test post
        // doesn't bring setup back.
        $hasPosted = (new Query())
            ->from(['{{%socialposter_posts}}'])
            ->where(['success' => true])
            ->exists();

        if ($hasPosted) {
            return null;
        }

        $accounts = SocialPoster::$plugin->getAccounts()->getAllAccounts();
        $readyAccounts = array_values(array_filter($accounts, fn($account) => $account instanceof OAuthAccount && $account->enabled && $account->isConfigured()));
        $connectedAccounts = array_values(array_filter($readyAccounts, fn(OAuthAccount $account) => $account->isConnected()));

        // A failed post doesn't end setup, but the last step links to it so its response is one click away.
        $failedPost = Post::find()
            ->status(null)
            ->orderBy(['elements.dateCreated' => SORT_DESC])
            ->one();

        // The account the steps talk about: a connected one, else a ready one, else the first one added.
        $account = $connectedAccounts[0] ?? $readyAccounts[0] ?? $accounts[0] ?? null;

        return [
            'account' => $account,
            'ready' => (bool)$readyAccounts,
            'connected' => (bool)$connectedAccounts,
            'failedPost' => $failedPost,
            'canPost' => (bool)SocialPoster::$plugin->getSettings()->enabledSections,
        ];
    }

}
