<?php
namespace verbb\socialposter\models;

use craft\base\Model;

class Payload extends Model
{
    // Properties
    // =========================================================================

    public ?string $title = null;
    public ?string $url = null;
    public ?string $message = null;
    public ?string $picture = null;

}