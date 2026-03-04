<?php

namespace GlueAgency\Elasticsearch\mappers\elements\fields;

use craft\base\Element;
use craft\base\Field;
use GlueAgency\Elasticsearch\factories\FieldMappingFactory;

class MatrixFieldMapper implements FieldMapperInterface
{

    public function format(Element $element, Field $field): mixed
    {
        $data = [];

        $fieldFormatterFactory = new FieldMappingFactory;
        foreach($element->getFieldValue($field->handle)->all() as $block) {
            foreach($block->getFieldLayout()->getCustomFields() as $field) {
                if($field->searchable) {
                    $formatted = $fieldFormatterFactory->format($block, $field);

                    $data[] = is_array($formatted) ? implode(' ', $formatted) : $formatted;
                }
            }
        }

        return array_filter($data);
    }
}
