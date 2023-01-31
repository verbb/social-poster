<?php
namespace verbb\socialposter\helpers;

use Craft;
use craft\fields\Assets;

class SocialPosterHelper
{
    // Static Methods
    // =========================================================================

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
