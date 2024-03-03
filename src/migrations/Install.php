<?php
namespace verbb\socialposter\migrations;

use craft\db\Migration;
use craft\helpers\MigrationHelper;

use verbb\auth\Auth;

class Install extends Migration
{
    // Public Methods
    // =========================================================================

    public function safeUp(): bool
    {
        // Ensure that the Auth module kicks off setting up tables
        // Use `Auth::getInstance()` not `Auth::$plugin` as it doesn't seem to work well in migrations
        Auth::getInstance()->migrator->up();

        $this->createTables();
        $this->createIndexes();
        $this->addForeignKeys();

        return true;
    }

    public function safeDown(): bool
    {
        $this->dropForeignKeys();
        $this->dropTables();

        // Delete all tokens for this plugin
        Auth::$plugin->getTokens()->deleteTokensByOwner('social-poster');

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
            'type' => $this->string()->notNull(),
            'settings' => $this->text(),
            'sortOrder' => $this->smallInteger()->unsigned(),
            'cache' => $this->text(),
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
