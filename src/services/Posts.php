<?php
namespace verbb\socialposter\services;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\elements\Post;

use Craft;
use craft\base\Component;

class Posts extends Component
{
    // Public Methods
    // =========================================================================

    public function getPostById(int $id, $siteId = null)
    {
        return Craft::$app->getElements()->getElementById($id, Post::class, $siteId);
    }

}
