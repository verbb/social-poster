<?php
namespace Craft;

class SocialPoster_PostsService extends BaseApplicationComponent
{
    // Public Methods
    // =========================================================================

    public function getAll($indexBy = null)
    {
        $records = SocialPoster_PostRecord::model()->ordered()->findAll();

        if ($records) {
            return SocialPoster_PostModel::populateModels($records, $indexBy);
        }
    }

    public function getAllByElementId($elementId)
    {
        $records = SocialPoster_PostRecord::model()->ordered()->findAllByAttributes(array('elementId' => $elementId));

        if ($records) {
            return SocialPoster_PostModel::populateModels($records, 'handle');
        }
    }

    public function getById($id)
    {
        $record = SocialPoster_PostRecord::model()->findById($id);

        if ($record) {
            return SocialPoster_PostModel::populateModel($record);
        }
    }

    public function getByHandle($handle)
    {
        $record = SocialPoster_PostRecord::model()->findByAttributes(array('handle' => $handle));

        if ($record) {
            return SocialPoster_PostModel::populateModel($record);
        }
    }

    public function save(SocialPoster_PostModel $model)
    {
        if ($model->id) {
            $record = SocialPoster_PostRecord::model()->findById($model->id);
        } else {
            $record = new SocialPoster_PostRecord();
        }

        $record->setAttributes($model->getAttributes(), false);

        $record->validate();
        $model->addErrors($record->getErrors());

        if ($model->hasErrors()) {
            return false;
        }

        $record->save(false);

        if (!$model->id) {
            $model->id = $record->id;
        }

        return true;
    }

}
