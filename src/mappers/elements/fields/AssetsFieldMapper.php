<?php

namespace GlueAgency\Elasticsearch\mappers\elements\fields;

use craft\base\Element;
use craft\base\Field;

class AssetsFieldMapper implements FieldMapperInterface
{

    public function format(Element $element, Field $field): mixed
    {
        $related = $element->getFieldValue($field->handle)->all();

        return array_column($related, 'title');
    }
}
