<?php
namespace verbb\socialposter\migrations;

use verbb\socialposter\elements\Post;

use Craft;
use craft\db\Migration;
use craft\db\Query;
use craft\elements\Entry;
use craft\helpers\ArrayHelper;
use craft\helpers\Json;
use craft\helpers\MigrationHelper;

class m190312_000000_craft_3 extends Migration
{
    // Public Methods
    // =========================================================================

    public function safeUp(): bool
    {
        // Create the new tokens table
        if (!$this->db->tableExists('{{%socialposter_tokens}}')) {
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
        }

        if ($this->db->tableExists('{{%socialposter_accounts}}')) {
            if (!$this->db->columnExists('{{%socialposter_accounts}}', 'name')) {
                $this->addColumn('{{%socialposter_accounts}}', 'name', $this->string()->notNull()->after('id'));
            }

            if (!$this->db->columnExists('{{%socialposter_accounts}}', 'enabled')) {
                $this->addColumn('{{%socialposter_accounts}}', 'enabled', $this->boolean()->after('handle'));
            }

            if (!$this->db->columnExists('{{%socialposter_accounts}}', 'autoPost')) {
                $this->addColumn('{{%socialposter_accounts}}', 'autoPost', $this->boolean()->after('enabled'));
            }

            if (!$this->db->columnExists('{{%socialposter_accounts}}', 'providerHandle')) {
                $this->addColumn('{{%socialposter_accounts}}', 'providerHandle', $this->string()->notNull()->after('autoPost'));
            }

            if (!$this->db->columnExists('{{%socialposter_accounts}}', 'sortOrder')) {
                $this->addColumn('{{%socialposter_accounts}}', 'sortOrder', $this->smallInteger()->unsigned()->after('providerSettings'));
            }

            $accounts = (new Query())
                ->select(['*'])
                ->from(['{{%socialposter_accounts}}'])
                ->all();

            $i = 1;
            foreach ($accounts as $account) {
                $settings = Json::decode($account['providerSettings']);

                $settings = ArrayHelper::firstValue($settings);
                $enabled = ArrayHelper::remove($settings, 'enabled');
                $autoPost = ArrayHelper::remove($settings, 'autoPost');

                $values = [
                    'name' => $account['handle'],
                    'enabled' => $enabled,
                    'autoPost' => $autoPost,
                    'providerHandle' => $account['handle'],
                    'providerSettings' => Json::encode($settings),
                    'sortOrder' => $i,
                ];

                $this->update('{{%socialposter_accounts}}', $values, ['id' => $account['id']], [], false);

                $i++;
            }

            if ($this->db->columnExists('{{%socialposter_accounts}}', 'providerSettings')) {
                MigrationHelper::renameColumn('{{%socialposter_accounts}}', 'providerSettings', 'settings', $this);
            }
        }

        // Move Oauth tokens to our token table
        if ($this->db->tableExists('{{%oauth_tokens}}')) {
            $tokens = (new Query())
                ->select(['*'])
                ->from(['{{%oauth_tokens}}'])
                ->where(['pluginHandle' => 'socialposter'])
                ->all();

            foreach ($tokens as $token) {
                $this->db->createCommand()->insert('{{%socialposter_tokens}}', [
                    'id' => $token['id'],
                    'providerHandle' => $token['providerHandle'],
                    'accessToken' => $token['accessToken'],
                    'secret' => $token['secret'],
                    'endOfLife' => $token['endOfLife'],
                    'refreshToken' => $token['refreshToken'],
                ])->execute();
            }
        }

        if ($this->db->tableExists('{{%socialposter_posts}}')) {
            MigrationHelper::dropAllForeignKeysOnTable('{{%socialposter_posts}}', $this);

            // Convert IDs to Element IDs (and create the elements)
            $this->convertPostsToElements();

            if (!$this->db->columnExists('{{%socialposter_posts}}', 'accountId')) {
                $this->addColumn('{{%socialposter_posts}}', 'accountId', $this->integer()->after('id'));

                $accounts = (new Query())
                    ->select(['id', 'handle'])
                    ->from(['{{%socialposter_accounts}}'])
                    ->pairs();

                foreach ($accounts as $id => $handle) {
                    $this->update('{{%socialposter_posts}}', ['accountId' => $id], ['handle' => $handle], [], false);
                }
            }

            if ($this->db->columnExists('{{%socialposter_posts}}', 'elementId')) {
                MigrationHelper::renameColumn('{{%socialposter_posts}}', 'elementId', 'ownerId', $this);
            }

            if ($this->db->columnExists('{{%socialposter_posts}}', 'handle')) {
                $this->dropColumn('{{%socialposter_posts}}', 'handle');
            }

            if (!$this->db->columnExists('{{%socialposter_posts}}', 'ownerSiteId')) {
                $this->addColumn('{{%socialposter_posts}}', 'ownerSiteId', $this->integer()->notNull()->after('ownerId'));

                $currentSiteId = Craft::$app->getSites()->getCurrentSite()->id;

                $this->update('{{%socialposter_posts}}', ['ownerSiteId' => $currentSiteId], [], [], false);
            }

            if (!$this->db->columnExists('{{%socialposter_posts}}', 'ownerType')) {
                $this->addColumn('{{%socialposter_posts}}', 'ownerType', $this->string()->notNull()->after('ownerSiteId'));

                $this->update('{{%socialposter_posts}}', ['ownerType' => Entry::class], [], [], false);
            }

            if ($this->db->columnExists('{{%socialposter_posts}}', 'providerSettings')) {
                MigrationHelper::renameColumn('{{%socialposter_posts}}', 'providerSettings', 'settings', $this);
            }

            $queryBuilder = $this->db->getSchema()->getQueryBuilder();
            $this->execute($queryBuilder->checkIntegrity(false));

            $this->addForeignKey(null, '{{%socialposter_posts}}', ['id'], '{{%elements}}', ['id'], 'CASCADE', null);
            $this->addForeignKey(null, '{{%socialposter_posts}}', ['ownerId'], '{{%elements}}', ['id'], 'CASCADE', null);

            // Re-enable FK checks
            $this->execute($queryBuilder->checkIntegrity(true));
        }

        return true;
    }

    public function safeDown(): bool
    {
        echo "m190312_000000_craft_3 cannot be reverted.\n";
        return false;
    }


    // Private Methods
    // =========================================================================

    private function convertPostsToElements(): void
    {
        $db = Craft::$app->getDb();

        $siteIds = (new Query())
            ->select(['id'])
            ->from(['{{%sites}}'])
            ->column($this->db);

        $postIds = (new Query())
            ->select(['id'])
            ->from(['{{%socialposter_posts}}'])
            ->all();

        foreach ($postIds as $postId) {
            $db->createCommand()->insert('{{%elements}}', [
                'type' => Post::class,
                'enabled' => 1,
                'archived' => 0,
            ])->execute();

            $elementId = $db->getLastInsertID();

            foreach ($siteIds as $siteId) {
                $db->createCommand()->insert('{{%elements_sites}}', [
                    'elementId' => $elementId,
                    'siteId' => $siteId,
                    'slug' => '',
                    'uri' => null,
                    'enabled' => 1,
                ])->execute();

                $db->createCommand()->insert('{{%content}}', [
                    'elementId' => $elementId,
                    'siteId' => $siteId,
                    'title' => '',
                ])->execute();
            }

            $this->update('{{%socialposter_posts}}', ['id' => $elementId], ['id' => $postId], [], false);
        }
    }
}
