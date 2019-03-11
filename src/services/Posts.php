<?php
namespace verbb\socialposter\services;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\elements\Post;

use Craft;
use craft\base\Component;

class Posts extends Component
{
    // Public Methods
    // =========================================================================

    public function getPostById(int $id, $siteId = null)
    {
        return Craft::$app->getElements()->getElementById($id, Post::class, $siteId);
    }

    public function renderEntrySidebar(&$context)
    {
        // $settings = Workflow::$plugin->getSettings();
        // $currentUser = Craft::$app->getUser()->getIdentity();

        // // if (!$settings->editorUserGroup || !$settings->publisherUserGroup) {
        // //     Workflow::log('Editor and Publisher groups not set in settings.');

        // //     return;
        // // }

        // $editorGroup = Craft::$app->userGroups->getGroupById($settings->editorUserGroup);
        // $publisherGroup = Craft::$app->userGroups->getGroupById($settings->publisherUserGroup);

        // if (!$currentUser) {
        //     Workflow::log('No current user.');

        //     return;
        // }

        // // Only show the sidebar submission button for editors
        // if ($currentUser->isInGroup($editorGroup)) {
        //     return $this->_renderEntrySidebarPanel($context, 'editor-pane');
        // }

        // // Show another information panel for publishers (if there's submission info)
        // if ($currentUser->isInGroup($publisherGroup)) {
        //     return $this->_renderEntrySidebarPanel($context, 'publisher-pane');
        // }
        $settings = SocialPoster::$plugin->getSettings();

        SocialPoster::log('Try to render sidebar');

        if (!$context['entry']->id) {
            SocialPoster::log('New entry.');

            return;
        }
        // Make sure workflow is enabled for this section - or all section
        if ($settings->enabledSections != '*') {
            if (!in_array($context['entry']->sectionId, $settings->enabledSections)) {
                SocialPoster::log('Entry not in allowed section.');

                return;
            }
        }



        $accounts = SocialPoster::$plugin->getAccounts()->getAllAccounts(true);

        $posts = [];

        // Remove any accounts that don't have settings - they haven't been configured!
        // foreach ($accounts as $key => $account) {
        //     if (!$account->providerSettings) {
        //         unset($accounts[$key]);
        //     }
        // }

        // See if there's an existing submission
        // $draftId = (isset($context['draftId'])) ? $context['draftId'] : ':empty:';
        // $siteId = (isset($context['entry']['siteId'])) ? $context['entry']['siteId'] : Craft::$app->getSites()->getCurrentSite()->id;
        // $submissions = Submission::find()
        //     ->ownerId($context['entry']->id)
        //     ->ownerSiteId($siteId)
        //     ->draftId($draftId)
        //     ->all();

        SocialPoster::log('Rendering #' . $context['entry']->id);

        return Craft::$app->view->renderTemplate('social-poster/_includes/entry-sidebar', [
            'context' => $context,
            'accounts' => $accounts,
            'posts' => $posts,
        ]);
    }

    // public function getAll($indexBy = null)
    // {
    //     $records = SocialPoster_PostRecord::model()->ordered()->findAll();

    //     if ($records) {
    //         return SocialPoster_PostModel::populateModels($records, $indexBy);
    //     }
    // }

    // public function getAllByElementId($elementId)
    // {
    //     $records = SocialPoster_PostRecord::model()->ordered()->findAllByAttributes(array('elementId' => $elementId));

    //     if ($records) {
    //         return SocialPoster_PostModel::populateModels($records, 'handle');
    //     }
    // }

    // public function getById($id)
    // {
    //     $record = SocialPoster_PostRecord::model()->findById($id);

    //     if ($record) {
    //         return SocialPoster_PostModel::populateModel($record);
    //     }
    // }

    // public function getByHandle($handle)
    // {
    //     $record = SocialPoster_PostRecord::model()->findByAttributes(array('handle' => $handle));

    //     if ($record) {
    //         return SocialPoster_PostModel::populateModel($record);
    //     }
    // }

    // public function save(SocialPoster_PostModel $model)
    // {
    //     if ($model->id) {
    //         $record = SocialPoster_PostRecord::model()->findById($model->id);
    //     } else {
    //         $record = new SocialPoster_PostRecord();
    //     }

    //     $record->setAttributes($model->getAttributes(), false);

    //     $record->validate();
    //     $model->addErrors($record->getErrors());

    //     if ($model->hasErrors()) {
    //         return false;
    //     }

    //     $record->save(false);

    //     if (!$model->id) {
    //         $model->id = $record->id;
    //     }

    //     return true;
    // }

}
