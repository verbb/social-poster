<?php
namespace verbb\socialposter\services;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\base\AccountInterface;
use verbb\socialposter\elements\Post;
use verbb\socialposter\models\Payload;

use Craft;
use craft\base\Component;
use craft\db\Table;
use craft\elements\Entry;
use craft\events\DefineHtmlEvent;
use craft\events\ModelEvent;
use craft\fields\Assets;
use craft\helpers\Db;
use craft\helpers\ElementHelper;
use craft\helpers\Json;

use Throwable;

class Service extends Component
{
    // Public Methods
    // =========================================================================

    public function renderEntrySidebar(DefineHtmlEvent $event): void
    {
        $entry = $event->sender->getCanonical();
        
        if (!$this->_isEnabledForEntry($entry)) {
            SocialPoster::info('Entry not in allowed section.');

            return;
        }

        $accounts = SocialPoster::$plugin->getAccounts()->getAllConfiguredAccounts();

        if (!$accounts) {
            SocialPoster::info('No accounts configured.');

            return;
        }

        $posts = [];

        if ($entry->id) {
            foreach ($accounts as $account) {
                $posts[$account->handle] = Post::find()
                    ->ownerId($entry->id)
                    ->ownerSiteId($entry->siteId)
                    ->orderBy('dateCreated desc')
                    ->accountId($account->id)
                    ->limit(1)
                    ->one();
            }
        }

        $event->html .= Craft::$app->getView()->renderTemplate('social-poster/_includes/entry-sidebar', [
            'entry' => $entry,
            'accounts' => $accounts,
            'posts' => $posts,
        ]);
    }

    public function onAfterSaveEntry(ModelEvent $event): void
    {
        $request = Craft::$app->getRequest();
        $templates = SocialPoster::$plugin->getTemplates();
        $elementsService = Craft::$app->getElements();
        $accountsService = SocialPoster::$plugin->getAccounts();

        /** @var Entry $entry */
        $entry = $event->sender;

        if ($entry->propagating || ElementHelper::isDraftOrRevision($entry)) {
            return;
        }

        // Check to make sure the entry is live
        if ($entry->status != Entry::STATUS_LIVE) {
            SocialPoster::info('Entry not set to live, skipping.');

            return;
        }

        if (!$this->_isEnabledForEntry($entry)) {
            SocialPoster::info('Entry not in allowed section.');

            return;
        }

        $chosenAccounts = $request->getParam('socialPoster');

        // Firstly, has the user selected any social media to post to?
        if (!is_array($chosenAccounts) || !$chosenAccounts) {
            SocialPoster::info('No accounts set to post to, skipping.');

            return;
        }

        foreach ($chosenAccounts as $accountHandle => $postChosenAccount) {
            if (!is_string($accountHandle) || !is_array($postChosenAccount)) {
                SocialPoster::info('Invalid account post data, skipping.');

                continue;
            }

            // Load in the defaults for this provider, as defined in Social Poster settings
            $configuredAccount = $accountsService->getAccountByHandle($accountHandle);

            if (!$configuredAccount || !$configuredAccount->enabled || !$configuredAccount->isConfigured()) {
                SocialPoster::info('Account ' . $accountHandle . ' is unavailable, skipping.');

                continue;
            }

            // Keep the configured account immutable and apply only per-post content overrides.
            $account = clone $configuredAccount;
            $this->_applyPostOverrides($account, $postChosenAccount);

            // Only post to the enabled ones
            if (!$account->autoPost) {
                SocialPoster::info('Account ' . $accountHandle . ' not set to autopost.');

                continue;
            }

            $payload = new Payload();
            $payload->element = $entry;
            $payload->title = $templates->renderSandboxedObjectTemplate((string)$account->title, $entry);
            $payload->url = $templates->renderSandboxedObjectTemplate((string)$account->url, $entry);
            $payload->message = $templates->renderSandboxedObjectTemplate((string)$account->message, $entry);

            if ($account->imageField) {
                try {
                    $asset = $entry->getFieldValue($account->imageField)->one();

                    if ($asset) {
                        $payload->picture = $asset->url;
                    }
                } catch (Throwable $e) {
                    SocialPoster::error('Unable to process asset: ' . $e->getMessage());
                }
            }

            // Make the actual social post
            $postResult = $account->sendPost($payload);

            // Save it to out Posts table - no matter the result
            $post = new Post();
            $post->ownerId = $entry->id;
            $post->ownerSiteId = $entry->siteId;
            $post->ownerType = $entry::class;
            $post->accountId = $account->id;
            $post->settings = $payload->toArray();
            $post->response = $postResult->response;
            $post->success = $postResult->success;
            $post->data = $postResult->data;

            if (!$elementsService->saveElement($post)) {
                SocialPoster::error('Unable to save post: ' . Json::encode($post->getErrors()));
            }
        }
    }


    // Private Methods
    // =========================================================================

    private function _applyPostOverrides(AccountInterface $account, array $overrides): void
    {
        if (array_key_exists('autoPost', $overrides) && is_scalar($overrides['autoPost'])) {
            $autoPost = filter_var($overrides['autoPost'], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);

            if ($autoPost !== null) {
                $account->autoPost = $autoPost;
            }
        }

        foreach (['title', 'url', 'message'] as $attribute) {
            if (!array_key_exists($attribute, $overrides)) {
                continue;
            }

            $value = $overrides[$attribute];

            if (is_scalar($value) || $value === null) {
                $account->$attribute = $value === null ? null : (string)$value;
            }
        }

        if (array_key_exists('imageField', $overrides) && is_scalar($overrides['imageField'])) {
            $imageFieldHandle = (string)$overrides['imageField'];
            $imageField = Craft::$app->getFields()->getFieldByHandle($imageFieldHandle);

            if ($imageFieldHandle === '' || $imageField instanceof Assets) {
                $account->imageField = $imageFieldHandle;
            }
        }
    }

    private function _isEnabledForEntry(Entry $entry): bool
    {
        $enabledSections = SocialPoster::$plugin->getSettings()->enabledSections;

        if (!$enabledSections) {
            return false;
        }

        if ($enabledSections === '*') {
            return true;
        }

        return in_array($entry->sectionId, Db::idsByUids(Table::SECTIONS, $enabledSections));
    }
}
