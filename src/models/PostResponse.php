<?php
namespace verbb\socialposter\models;

use craft\base\Model;

class PostResponse extends Model
{
    // Properties
    // =========================================================================

    public ?bool $success = null;
    public array $response = [];
    public array $data = [];

}