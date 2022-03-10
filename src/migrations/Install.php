<?php
namespace verbb\socialposter\migrations;

use craft\db\Migration;
use craft\helpers\Db;

class Install extends Migration
{
    // Public Methods
    // =========================================================================

    public function safeUp(): bool
    {
        $this->createTables();
        $this->createIndexes();
        $this->addForeignKeys();

        return true;
    }

    public function safeDown(): bool
    {
        $this->dropForeignKeys();
        $this->dropTables();

        return false;
    }

    public function createTables(): void
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

    public function dropTables(): void
    {
        $this->dropTable('{{%socialposter_accounts}}');
        $this->dropTable('{{%socialposter_tokens}}');
        $this->dropTable('{{%socialposter_posts}}');
    }


    // Protected Methods
    // =========================================================================

    protected function createIndexes(): void
    {
        $this->createIndex(null, '{{%socialposter_accounts}}', ['name'], true);
        $this->createIndex(null, '{{%socialposter_accounts}}', ['handle'], true);
        $this->createIndex(null, '{{%socialposter_posts}}', ['ownerId'], false);
    }

    protected function addForeignKeys(): void
    {
        $this->addForeignKey(null, '{{%socialposter_posts}}', ['id'], '{{%elements}}', ['id'], 'CASCADE', null);
        $this->addForeignKey(null, '{{%socialposter_posts}}', ['ownerId'], '{{%elements}}', ['id'], 'CASCADE', null);
    }

    protected function dropForeignKeys(): void
    {
        Db::dropForeignKeyIfExists('{{%socialposter_posts}}', ['id'], $this);
        Db::dropForeignKeyIfExists('{{%socialposter_posts}}', ['ownerId'], $this);
    }
}
