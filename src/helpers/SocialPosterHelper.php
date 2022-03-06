<?php
namespace verbb\socialposter\helpers;

use Craft;
use craft\fields\Assets;
use craft\helpers\UrlHelper;

class SocialPosterHelper
{
    // Static Methods
    // =========================================================================

    public static function siteActionUrl(string $path = '', $params = null, string $protocol = null): string
    {
        // Force `addTrailingSlashesToUrls` to `false` while we generate the redirectUri
        $addTrailingSlashesToUrls = Craft::$app->getConfig()->getGeneral()->addTrailingSlashesToUrls;
        Craft::$app->getConfig()->getGeneral()->addTrailingSlashesToUrls = false;

        $redirectUri = UrlHelper::actionUrl($path, $params, $protocol);

        // Set `addTrailingSlashesToUrls` back to its original value
        Craft::$app->getConfig()->getGeneral()->addTrailingSlashesToUrls = $addTrailingSlashesToUrls;

        // We don't want the CP trigger showing in the action URL.
        return str_replace(Craft::$app->getConfig()->getGeneral()->cpTrigger . '/', '', $redirectUri);
    }

    public static function getAssetFieldOptions(): array
    {
        $imageOptions = [];
        $imageOptions[] = ['label' => Craft::t('social-poster', 'Select field'), 'value' => ''];
        
        foreach (Craft::$app->getFields()->getAllFields() as $field) {
            if ($field::class === Assets::class) {
                $imageOptions[] = ['label' => $field->name, 'value' => $field->handle];
            }
        }

        return $imageOptions;
    }
}
