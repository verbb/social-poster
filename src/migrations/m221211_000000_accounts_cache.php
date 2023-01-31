<?php
namespace verbb\socialposter\migrations;

use craft\db\Migration;

class m221211_000000_accounts_cache extends Migration
{
    public function safeUp(): bool
    {
        if (!$this->db->columnExists('{{%socialposter_accounts}}', 'cache')) {
            $this->addColumn('{{%socialposter_accounts}}', 'cache', $this->text()->after('sortOrder'));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m221211_000000_accounts_cache cannot be reverted.\n";
        return false;
    }
}
