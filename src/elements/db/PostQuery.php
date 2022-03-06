<?php
namespace verbb\socialposter\elements\db;

use craft\base\ElementInterface;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;

class PostQuery extends ElementQuery
{
    // Properties
    // =========================================================================

    public mixed $id = null;
    public mixed $ownerId = null;
    public mixed $ownerSiteId = null;
    public mixed $accountId = null;
    public mixed $response = null;


    // Public Methods
    // =========================================================================

    public function ownerId($value): static
    {
        $this->ownerId = $value;
        return $this;
    }

    public function ownerSiteId($value): static
    {
        $this->ownerSiteId = $value;
        return $this;
    }

    public function accountId($value): static
    {
        $this->accountId = $value;
        return $this;
    }

    public function account(ElementInterface $account): static
    {
        $this->accountId = $account->id;
        return $this;
    }


    // Protected Methods
    // =========================================================================

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('socialposter_posts');
            
        $this->query->select([
            'socialposter_posts.id',
            'socialposter_posts.accountId',
            'socialposter_posts.ownerId',
            'socialposter_posts.ownerSiteId',
            'socialposter_posts.ownerType',
            'socialposter_posts.settings',
            'socialposter_posts.success',
            'socialposter_posts.response',
            'socialposter_posts.data',
        ]);

        $this->addWhere('id', 'socialposter_posts.id');
        $this->addWhere('ownerId', 'socialposter_posts.ownerId');
        $this->addWhere('ownerSiteId', 'socialposter_posts.ownerSiteId');
        $this->addWhere('accountId', 'socialposter_posts.accountId');
        $this->addWhere('response', 'socialposter_posts.response');

        return parent::beforePrepare();
    }


    // Private Methods
    // =========================================================================

    private function addWhere(string $property, string $column): void
    {
        if ($this->{$property}) {
            $this->subQuery->andWhere(Db::parseParam($column, $this->{$property}));
        }
    }
}
