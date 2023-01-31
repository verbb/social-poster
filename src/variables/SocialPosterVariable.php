<?php
namespace verbb\socialposter\variables;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\elements\Post;
use verbb\socialposter\elements\db\PostQuery;

use Craft;

class SocialPosterVariable
{
    // Public Methods
    // =========================================================================

    public function getPlugin(): SocialPoster
    {
        return SocialPoster::$plugin;
    }

    public function getPluginName(): string
    {
        return SocialPoster::$plugin->getPluginName();
    }

    public function posts($criteria = null): PostQuery
    {
        $query = Post::find();

        if ($criteria) {
            Craft::configure($query, $criteria);
        }

        return $query;
    }

}