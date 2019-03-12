<?php
namespace verbb\socialposter\services;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\elements\Post;

use Craft;
use craft\base\Component;
use craft\db\Table;
use craft\elements\Entry;
use craft\events\ModelEvent;
use craft\helpers\Db;
use craft\helpers\UrlHelper;

class Service extends Component
{
    // Public Methods
    // =========================================================================

    public function renderEntrySidebar(&$context)
    {
        $settings = SocialPoster::$plugin->getSettings();

        SocialPoster::log('Try to render sidebar');

        // Make sure social poster is enabled for this section - or all section
        if (!$settings->enabledSections) {
            SocialPoster::log('New enabled sections.');

            return;
        }
        
        if ($settings->enabledSections != '*') {
            $enabledSectionIds = Db::idsByUids(Table::SECTIONS, $settings->enabledSections);

            if (!in_array($context['entry']->sectionId, $enabledSectionIds)) {
                SocialPoster::log('Entry not in allowed section.');

                return;
            }
        }

        $accounts = SocialPoster::$plugin->getAccounts()->getAllAccounts();

        // Remove any accounts that don't have settings - they haven't been configured!
        foreach ($accounts as $key => $account) {
            if (!$account->settings) {
                SocialPoster::log('Account ' . $key . ' not configured.');

                unset($accounts[$key]);
            }
        }

        if (!$accounts) {
            SocialPoster::log('No accounts configured.');

            return;
        }

        $posts = Post::find()
            ->ownerId($context['entry']->id)
            ->ownerSiteId($context['entry']->siteId)
            ->indexBy('accountId')
            ->orderBy('dateCreated asc')
            ->all();

        SocialPoster::log('Rendering #' . $context['entry']->id);

        return Craft::$app->view->renderTemplate('social-poster/_includes/entry-sidebar', [
            'context' => $context,
            'accounts' => $accounts,
            'posts' => $posts,
        ]);
    }

    public function onAfterSaveEntry(ModelEvent $event)
    {
        $request = Craft::$app->getRequest();

        $entry = $event->sender;

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
            $account = SocialPoster::$plugin->getAccounts()->getAccountByHandle($accountHandle);
            $settings = $account->settings;

            // Allow posted data to override anything in our defaults
            $payload = array_merge($settings, $postChosenAccount);

            // Only post to the enabled ones
            if (!$payload['autoPost']) {
                SocialPoster::log('Account ' . $accountHandle . ' not set to autopost.');

                continue;
            }
                
            // Get the actual text for the post
            $message = $payload['message'];
            $message = Craft::$app->getView()->renderObjectTemplate($message, $entry);
            $payload['message'] = $message; // update 'model'

            // TODO - testing
            // $payload['message'] .= rand();

            // Get the image (if one)
            $payload['picture'] = '';
            
            if (isset($payload['imageField']) && $payload['imageField']) {
                $assetIds = $entry->content[$payload['imageField']];

                if (is_array($assetIds) && isset($assetIds[0])) {
                    $assetId = $assetIds[0];

                    $asset = Craft::$app->getAssets()->getAssetById($assetId);
                    
                    // Handle absolute URL
                    $siteUrl = UrlHelper::siteUrl();
                    
                    if (($siteUrl[strlen($siteUrl) -1] == '/') && ($asset->url[0] == '/')) {
                        $siteUrl = rtrim($siteUrl, '/');
                    }
                    
                    $payload['picture'] = $siteUrl . $asset->url;
                }
            }

            // Make the actual social post
            $postResult = $account->provider->sendPost($account, $payload);

            // Save it to out Posts table - no matter the result
            if ($postResult) {
                $post = new Post();
                $post->ownerId = $entry->id;
                $post->ownerSiteId = $entry->siteId;
                $post->ownerType = get_class($entry);
                $post->accountId = $account->id;
                $post->settings = $payload;
                $post->response = $postResult['response'] ?? [];
                $post->success = $postResult['success'] ?? [];
                $post->data = $postResult['data'] ?? [];

                if (!Craft::$app->getElements()->saveElement($post)) {
                    SocialPoster::error('Unable to save post: ' . json_encode($post->getErrors()));
                }
            } else {
                SocialPoster::error('Unknown result for post: ' . json_encode($postResult));
            }
        }
    }
}