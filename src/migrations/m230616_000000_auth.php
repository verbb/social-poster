<?php
namespace verbb\socialposter\migrations;

use craft\db\Migration;

use verbb\auth\Auth;

class m230616_000000_auth extends Migration
{
    public function safeUp(): bool
    {
        if (!$this->db->tableExists('{{%auth_oauth_tokens}}')) {
            Auth::$plugin->migrator->up();
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m230616_000000_auth cannot be reverted.\n";
        return false;
    }
}
