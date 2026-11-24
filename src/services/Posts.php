<?php
namespace verbb\socialposter\services;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\base\OAuthAccount;
use verbb\socialposter\elements\Post;

use Craft;
use craft\base\Component;
use craft\db\Query;

use DateTime;
use DateTimeZone;

class Posts extends Component
{
    // Constants
    // =========================================================================

    // The day the Posts setup checklist was added. Only installs from then on show it.
    public const SETUP_CHECKLIST_SINCE = '2026-11-23';


    // Public Methods
    // =========================================================================

    public function getPostById(int $id, $siteId = null): ?Post
    {
        return Craft::$app->getElements()->getElementById($id, Post::class, $siteId);
    }

    /**
     * Returns what the Posts page's setup checklist needs, or null once it has no reason to show.
     */
    public function getSetupSteps(): ?array
    {
        if (!$this->_isNewInstall()) {
            return null;
        }

        // The checklist leaves at the first post the Posts page lists, failed ones included, since that page shows a
        // failure's response. Read the table rather than the element query, so a trashed post still counts and
        // deleting a test post doesn't bring setup back.
        $hasPosts = (new Query())
            ->from(['{{%socialposter_posts}}'])
            ->exists();

        if ($hasPosts) {
            return null;
        }

        $accounts = SocialPoster::$plugin->getAccounts()->getAllAccounts();
        $readyAccounts = array_values(array_filter($accounts, fn($account) => $account instanceof OAuthAccount && $account->enabled && $account->isConfigured()));
        $connectedAccounts = array_values(array_filter($readyAccounts, fn(OAuthAccount $account) => $account->isConnected()));

        // The account the steps talk about: a connected one, else a ready one, else the first one added.
        $account = $connectedAccounts[0] ?? $readyAccounts[0] ?? $accounts[0] ?? null;

        return [
            'account' => $account,
            'ready' => (bool)$readyAccounts,
            'connected' => (bool)$connectedAccounts,
            'canPost' => (bool)SocialPoster::$plugin->getSettings()->enabledSections,
        ];
    }


    // Private Methods
    // =========================================================================

    /**
     * Whether Social Poster was installed since the setup checklist was added, so sites that update never see it.
     * A missing install date counts as an existing install.
     */
    private function _isNewInstall(): bool
    {
        $installDate = Craft::$app->getPlugins()->getStoredPluginInfo('social-poster')['installDate'] ?? null;

        if (!$installDate instanceof DateTime) {
            return false;
        }

        // Craft stores the install date in UTC.
        return $installDate >= new DateTime(self::SETUP_CHECKLIST_SINCE, new DateTimeZone('UTC'));
    }

}
