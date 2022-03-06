<?php
namespace verbb\socialposter\records;

use craft\db\ActiveQuery;
use craft\db\ActiveRecord;
use craft\records\Element;

class Post extends ActiveRecord
{
    // Public Methods
    // =========================================================================

    public static function tableName(): string
    {
        return '{{%socialposter_posts}}';
    }

    public function getElement(): ActiveQuery
    {
        return $this->hasOne(Element::class, ['id' => 'id']);
    }

    public function getOwner(): ActiveQuery
    {
        return $this->hasOne(Element::class, ['id' => 'ownerId']);
    }
}
