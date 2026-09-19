<?php
namespace modules\socialposterscreenshots;

use craft\events\RegisterComponentTypesEvent;
use verbb\socialposter\services\Accounts;
use yii\base\Event;
use yii\base\Module as BaseModule;

class Module extends BaseModule
{
    public function init(): void
    {
        parent::init();

        Event::on(
            Accounts::class,
            Accounts::EVENT_REGISTER_ACCOUNT_TYPES,
            static function(RegisterComponentTypesEvent $event): void {
                $event->types[] = ScreenshotFacebookAccount::class;
                $event->types[] = ScreenshotInstagramAccount::class;
                $event->types[] = ScreenshotLinkedInAccount::class;
                $event->types[] = ScreenshotTwitterAccount::class;
            },
        );
    }
}
