<?php
namespace verbb\socialposter\elements\db;

use Craft;
use craft\db\Query;
// use craft\db\QueryAbortedException;
use craft\elements\db\ElementQuery;
// use craft\helpers\ArrayHelper;
use craft\helpers\Db;
// use craft\helpers\StringHelper;

use yii\db\Connection;

class PostQuery extends ElementQuery
{
    // Properties
    // =========================================================================

    public $id;
    public $ownerId;
    public $accountId;
    public $response;
    // public $provider;
    // public $enabled = true;


    // Public Methods
    // =========================================================================

    // public function elementId($value)
    // {
    //     $this->elementId = $value;
    //     return $this;
    // }

    // public function elementSiteId($value)
    // {
    //     $this->elementSiteId = $value;
    //     return $this;
    // }

    // public function elementClass($value)
    // {
    //     $this->elementClass = $value;
    //     return $this;
    // }

    public function accountId($value)
    {
        $this->accountId = $value;
        return $this;
    }

    public function account(ElementInterface $account)
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
        $this->addWhere('accountId', 'socialposter_posts.accountId');
        $this->addWhere('response', 'socialposter_posts.response');

        return parent::beforePrepare();
    }


    // Private Methods
    // =========================================================================

    private function addWhere(string $property, string $column)
    {
        if ($this->{$property}) {
            $this->subQuery->andWhere(Db::parseParam($column, $this->{$property}));
        }
    }
}
