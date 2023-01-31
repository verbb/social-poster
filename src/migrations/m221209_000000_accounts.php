<?php
namespace verbb\socialposter\migrations;

use verbb\socialposter\accounts as accountTypes;

use craft\db\Migration;
use craft\db\Query;

class m221209_000000_accounts extends Migration
{
    public function safeUp(): bool
    {
        $accounts = (new Query())
            ->select(['id', 'providerHandle'])
            ->from(['{{%socialposter_accounts}}'])
            ->all();

        foreach ($accounts as $account) {
            $oldType = $account['providerHandle'] ?? null;
            $type = null;

            if ($oldType === 'facebook') {
                $type = accountTypes\Facebook::class;
            }

            if ($oldType === 'linkedin') {
                $type = accountTypes\LinkedIn::class;
            }

            if ($oldType === 'twitter') {
                $type = accountTypes\Twitter::class;
            }
            
            $this->update('{{%socialposter_accounts}}', [
                'providerHandle' => $type,
            ], ['id' => $account['id']]);
        }

        $this->renameColumn('{{%socialposter_accounts}}', 'providerHandle', 'type');

        $this->dropTableIfExists('{{%socialposter_tokens}}');

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m221209_000000_accounts cannot be reverted.\n";
        return false;
    }
}
