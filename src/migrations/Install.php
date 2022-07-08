<?php
namespace verbb\socialposter\migrations;

use craft\db\Migration;
use craft\helpers\Db;
use craft\helpers\MigrationHelper;

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

        return true;
    }

    public function createTables(): void
    {
        $this->archiveTableIfExists('{{%socialposter_accounts}}');
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

        $this->archiveTableIfExists('{{%socialposter_tokens}}');
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

        $this->archiveTableIfExists('{{%socialposter_posts}}');
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

    public function createIndexes(): void
    {
        $this->createIndex(null, '{{%socialposter_accounts}}', ['name'], true);
        $this->createIndex(null, '{{%socialposter_accounts}}', ['handle'], true);
        $this->createIndex(null, '{{%socialposter_posts}}', ['ownerId'], false);
    }

    public function addForeignKeys(): void
    {
        $this->addForeignKey(null, '{{%socialposter_posts}}', ['id'], '{{%elements}}', ['id'], 'CASCADE', null);
        $this->addForeignKey(null, '{{%socialposter_posts}}', ['ownerId'], '{{%elements}}', ['id'], 'CASCADE', null);
    }

    public function dropTables(): void
    {
        $this->dropTableIfExists('{{%socialposter_accounts}}');
        $this->dropTableIfExists('{{%socialposter_tokens}}');
        $this->dropTableIfExists('{{%socialposter_posts}}');
    }

    public function dropForeignKeys(): void
    {
        if ($this->db->tableExists('{{%socialposter_posts}}')) {
            MigrationHelper::dropAllForeignKeysOnTable('{{%socialposter_posts}}', $this);
        }

        if ($this->db->tableExists('{{%socialposter_posts}}')) {
            MigrationHelper::dropAllForeignKeysOnTable('{{%socialposter_posts}}', $this);
        }
    }
}
