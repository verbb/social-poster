<?php
namespace verbb\socialposter\models;

use craft\base\ElementInterface;
use craft\base\Model;

class Payload extends Model
{
    // Properties
    // =========================================================================

    public ?ElementInterface $element = null;
    public ?string $title = null;
    public ?string $url = null;
    public ?string $message = null;
    public ?string $picture = null;

}