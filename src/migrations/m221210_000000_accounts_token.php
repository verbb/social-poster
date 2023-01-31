<?php
namespace verbb\socialposter\migrations;

use craft\db\Migration;

class m221210_000000_accounts_token extends Migration
{
    public function safeUp(): bool
    {
        if ($this->db->columnExists('{{%socialposter_accounts}}', 'tokenId')) {
            $this->dropColumn('{{%socialposter_accounts}}', 'tokenId');
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m221210_000000_accounts_token cannot be reverted.\n";
        return false;
    }
}
