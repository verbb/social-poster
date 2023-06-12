<?php
namespace verbb\socialposter\events;

use verbb\socialposter\base\AccountInterface;

use craft\base\ElementInterface;
use craft\events\CancelableEvent;

class SendPostEvent extends CancelableEvent
{
    // Properties
    // =========================================================================

    public ?ElementInterface $element = null;
    public ?AccountInterface $account = null;
    public mixed $payload = null;
    public mixed $response = null;
    public ?string $endpoint = null;
    public ?string $method = null;

}
