<?php

namespace GlueAgency\Elasticsearch\mappers\elements\fields;

use craft\base\Element;
use craft\base\Field;

class EntriesFieldMapper implements FieldMapperInterface
{

    public function format(Element $element, Field $field): mixed
    {
        $related = $element->getFieldValue($field->handle)->all();

        return array_map(function($item) {
            return [
                'id'    => $item->id,
                'title' => $item->title,
            ];
        }, $related);
    }
}
