<?php
namespace verbb\socialposter\migrations;

use Craft;
use craft\db\Migration;
use craft\helpers\MigrationHelper;

class Install extends Migration
{
    // Public Methods
    // =========================================================================

    public function safeUp()
    {
        $this->createTables();
        $this->createIndexes();
        $this->addForeignKeys();
    }

    public function safeDown()
    {
        $this->dropForeignKeys();
        $this->dropTables();
    }

    public function createTables()
    {
        $this->createTable('{{%socialposter_accounts}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'handle' => $this->string()->notNull(),
            'enabled' => $this->boolean(),
            'autoPost' => $this->boolean(),
            'providerHandle' => $this->string()->notNull(),
            'settings' => $this->text(),
            'sortOrder' => $this->smallInteger()->unsigned(),
            'tokenId' => $this->integer(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->createTable('{{%socialposter_tokens}}', [
            'id' => $this->primaryKey(),
            'providerHandle' => $this->string()->notNull(),
            'accessToken' => $this->text(),
            'secret' => $this->text(),
            'endOfLife' => $this->string(),
            'refreshToken' => $this->text(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->createTable('{{%socialposter_posts}}', [
            'id' => $this->primaryKey(),
            'accountId' => $this->integer()->notNull(),
            'ownerId' => $this->integer()->notNull(),
            'ownerSiteId' => $this->integer()->notNull(),
            'ownerType' => $this->string()->notNull(),
            'settings' => $this->text(),
            'success' => $this->boolean(),
            'response' => $this->text(),
            'data' => $this->text(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);
    }
    
    public function dropTables()
    {
        $this->dropTable('{{%socialposter_accounts}}');
        $this->dropTable('{{%socialposter_tokens}}');
        $this->dropTable('{{%socialposter_posts}}');
    }
    
    protected function createIndexes()
    {
        $this->createIndex(null, '{{%socialposter_accounts}}', ['name'], true);
        $this->createIndex(null, '{{%socialposter_accounts}}', ['handle'], true);
        $this->createIndex(null, '{{%socialposter_posts}}', ['ownerId'], false);
    }

    protected function addForeignKeys()
    {
        $this->addForeignKey(null, '{{%socialposter_posts}}', ['id'], '{{%elements}}', ['id'], 'CASCADE', null);
        $this->addForeignKey(null, '{{%socialposter_posts}}', ['ownerId'], '{{%elements}}', ['id'], 'CASCADE', null);
    }
    
    protected function dropForeignKeys()
    {
        MigrationHelper::dropForeignKeyIfExists('{{%socialposter_posts}}', ['id'], $this);
        MigrationHelper::dropForeignKeyIfExists('{{%socialposter_posts}}', ['ownerId'], $this);
    }
}
