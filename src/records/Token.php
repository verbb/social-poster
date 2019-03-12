<?php
namespace verbb\socialposter\records;

use craft\db\ActiveRecord;

use yii\db\ActiveQueryInterface;

class Token extends ActiveRecord
{
    // Public Methods
    // =========================================================================

    public static function tableName(): string
    {
        return '{{%socialposter_tokens}}';
    }
}
