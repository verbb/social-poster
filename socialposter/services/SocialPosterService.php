<?php
namespace Craft;

class SocialPosterService extends BaseApplicationComponent
{
    // Public Methods
    // =========================================================================

    public function getPlugin()
    {
        return craft()->plugins->getPlugin('socialPoster');
    }

    public function getSettings()
    {
        return $this->getPlugin()->getSettings();
    }

    public function renderEntrySidebar()
    {
        $settings = $this->getSettings();
        $user = craft()->userSession->getUser();

        // Catch some cases where there isn't a user. This can happen when sessions time out
        if (!$user) {
            return false;
        }

        $this->_renderEntrySidebarPanel();
    }

    public function onSaveEntry($event)
    {
        $entry = $event->params['entry'];

        // Check to make sure the entry is live
        if ($entry->status != 'live') {
            return false;
        }

        $chosenProviders = craft()->request->getPost('socialPoster');

        // Firstly, has the user selected any social media to post to?
        if (!$chosenProviders) {
            return false;
        }

        foreach ($chosenProviders as $providerHandle => $postChosenProvider) {

            // Load in the defaults for this provider, as defined in Social Poster settings
            $account = craft()->socialPoster_accounts->getByHandle($providerHandle);
            $chosenProvider = $account->providerSettings[$providerHandle];

            // Allow posted data to override anything in our defaults
            $chosenProvider = array_merge($chosenProvider, $postChosenProvider);

            // Only post to the enabled ones
            if (!$chosenProvider['autoPost']) {
                continue;
            }
                
            // Get the actual text for the post
            $message = $chosenProvider['message'];
            $message = craft()->templates->renderObjectTemplate($message, $entry);
            $message = craft()->config->parseEnvironmentString($message);
            $chosenProvider['message'] = $message; // update 'model'

            // Get the image (if one)
            $picture = null;
            if (isset($chosenProvider['imageField']) && $chosenProvider['imageField']) {
                $assetIds = $entry->content[$chosenProvider['imageField']];

                if (is_array($assetIds)) {
                    if (isset($assetIds[0])) {
                        $assetId = $assetIds[0];

                        $asset = craft()->assets->getFileById($assetId);
                        
                        // Handle absolute URL
                        $siteUrl = craft()->getSiteUrl();
                        
                        if (($siteUrl[strlen($siteUrl) -1] == '/') && ($asset->url[0] == '/')) {
                            $siteUrl = rtrim($siteUrl, '/');
                        }
                        
                        $picture = $siteUrl . $asset->url;
                    }
                }
            }

            // Make the actual social post
            $this->sendSocialPost($entry, $chosenProvider, $providerHandle, $picture);
        }
    }

    public function sendSocialPost($entry, $provider, $providerHandle, $picture)
    {
        $message = $provider['message'];

        $token = craft()->socialPoster_accounts->getToken($providerHandle);
        $accessToken = $token->accessToken;

        // Get the payload to post this to social media
        if ($providerHandle == 'facebook') {
            $postResult = craft()->socialPoster_facebook->getPayload($entry, $accessToken, $message, $picture, $provider);
        } else if ($providerHandle == 'twitter') {
            $postResult = craft()->socialPoster_twitter->getPayload($entry, $accessToken, $message, $picture, $provider);
        } else if ($providerHandle == 'linkedin') {
            $postResult = craft()->socialPoster_linkedIn->getPayload($entry, $accessToken, $message, $picture, $provider);
        }

        // Ssave it to out Posts table - no matter the result
        if (isset($postResult)) {
            $model = new SocialPoster_PostModel();
            $model->elementId = $entry->id;
            $model->handle = $providerHandle;
            $model->providerSettings = $provider;

            if (isset($postResult['response'])) {
                $model->response = $postResult['response'];
            }

            if (isset($postResult['success'])) {
                $model->success = $postResult['success'];
            }

            if (isset($postResult['data'])) {
                $model->data = $postResult['data'];
            }

            craft()->socialPoster_posts->save($model);
        }
    }



    // Private Methods
    // =========================================================================

    private function _renderEntrySidebarPanel()
    {
        $settings = $this->getSettings();

        craft()->templates->hook('cp.entries.edit.right-pane', function(&$context) use ($settings) {
            // Make sure social poster is enabled for this section - or all section
            if ($settings->enabledSections != '*') {
                if (!in_array($context['entry']->sectionId, $settings->enabledSections)) {
                    return;
                }
            }

            $accounts = craft()->socialPoster_accounts->getAll();

            // Remove any accounts that don't have settings - they haven't been configured!
            foreach ($accounts as $key => $account) {
                if (!$account->providerSettings) {
                    unset($accounts[$key]);
                }
            }

            if (!$accounts) {
                return;
            }

            $posts = craft()->socialPoster_posts->getAllByElementId($context['entry']->id);

            return craft()->templates->render('socialPoster/_includes/entry-right', array(
                'context' => $context,
                'accounts' => $accounts,
                'posts' => $posts,
            ));
        });
    }


}