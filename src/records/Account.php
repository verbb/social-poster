<?php
namespace verbb\socialposter\records;

use craft\db\ActiveRecord;

class Account extends ActiveRecord
{
    // Public Methods
    // =========================================================================

    public static function tableName(): string
    {
        return '{{%socialposter_accounts}}';
    }
}
