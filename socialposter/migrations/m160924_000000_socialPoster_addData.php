<?php
namespace Craft;

class m160924_000000_socialPoster_addData extends BaseMigration
{
    public function safeUp()
    {
        craft()->db->createCommand()->addColumnAfter('socialposter_posts', 'data', ColumnType::Text, 'response');

        return true;
    }
}
