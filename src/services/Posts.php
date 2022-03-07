<?php
namespace verbb\socialposter\services;

use verbb\socialposter\elements\Post;

use Craft;
use craft\base\Component;
use craft\base\ElementInterface;

class Posts extends Component
{
    // Public Methods
    // =========================================================================

    public function getPostById(int $id, $siteId = null): ?Post
    {
        /* @noinspection PhpIncompatibleReturnTypeInspection */
        return Craft::$app->getElements()->getElementById($id, Post::class, $siteId);
    }

}
