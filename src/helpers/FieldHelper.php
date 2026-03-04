<?php

namespace GlueAgency\Elasticsearch\helpers;

use craft\base\FieldInterface;
use craft\helpers\StringHelper;

class FieldHelper
{

    public static function toElasticSafeName(FieldInterface $field): string
    {
        return StringHelper::toSnakeCase($field->handle);
    }
}
