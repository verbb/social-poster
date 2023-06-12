<?php
namespace verbb\socialposter\services;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\elements\Post;
use verbb\socialposter\models\Payload;

use Craft;
use craft\base\Component;
use craft\db\Table;
use craft\elements\Entry;
use craft\events\DefineHtmlEvent;
use craft\events\ModelEvent;
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
        
        $settings = SocialPoster::$plugin->getSettings();

        // Make sure social poster is enabled for this section - or all section
        if (!$settings->enabledSections) {
            SocialPoster::log('New enabled sections.');

            return;
        }

        if ($settings->enabledSections != '*') {
            $enabledSectionIds = Db::idsByUids(Table::SECTIONS, $settings->enabledSections);

            if (!in_array($entry->sectionId, $enabledSectionIds)) {
                SocialPoster::log('Entry not in allowed section.');

                return;
            }
        }

        $accounts = SocialPoster::$plugin->getAccounts()->getAllConfiguredAccounts();

        if (!$accounts) {
            SocialPoster::log('No accounts configured.');

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
        $view = Craft::$app->getView();
        $elementsService = Craft::$app->getElements();
        $accountsService = SocialPoster::$plugin->getAccounts();

        /** @var Entry $entry */
        $entry = $event->sender;

        if ($entry->propagating || ElementHelper::isDraftOrRevision($entry)) {
            return;
        }

        // Check to make sure the entry is live
        if ($entry->status != Entry::STATUS_LIVE) {
            SocialPoster::log('Entry not set to live, skipping.');

            return;
        }

        $chosenAccounts = $request->getParam('socialPoster');

        // Firstly, has the user selected any social media to post to?
        if (!$chosenAccounts) {
            SocialPoster::log('No accounts set to post to, skipping.');

            return;
        }

        foreach ($chosenAccounts as $accountHandle => $postChosenAccount) {
            // Load in the defaults for this provider, as defined in Social Poster settings
            $account = $accountsService->getAccountByHandle($accountHandle);

            // Allow posted data to override anything in our defaults
            Craft::configure($account, $postChosenAccount);

            // Only post to the enabled ones
            if (!$account->autoPost) {
                SocialPoster::log('Account ' . $accountHandle . ' not set to autopost.');

                continue;
            }

            $payload = new Payload();
            $payload->element = $entry;
            $payload->title = $view->renderObjectTemplate((string)$account->title, $entry);
            $payload->url = $view->renderObjectTemplate((string)$account->url, $entry);
            $payload->message = $view->renderObjectTemplate((string)$account->message, $entry);

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
}