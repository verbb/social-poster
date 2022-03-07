<?php
namespace verbb\socialposter\variables;

use verbb\socialposter\SocialPoster;
use verbb\socialposter\elements\Post;
use verbb\socialposter\elements\db\PostQuery;
use verbb\socialposter\helpers\SocialPosterHelper;

use Craft;
use craft\elements\db\ElementQueryInterface;

class SocialPosterVariable
{
    // Public Methods
    // =========================================================================

    public function getPluginName(): string
    {
        return SocialPoster::$plugin->getPluginName();
    }

    public function getAssetFieldOptions(): array
    {
        return SocialPosterHelper::getAssetFieldOptions();
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