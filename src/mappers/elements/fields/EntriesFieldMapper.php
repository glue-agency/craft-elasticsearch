<?php

namespace GlueAgency\Elasticsearch\mappers\elements\fields;

use Craft;
use craft\base\Element;
use craft\base\Field;
use GlueAgency\Elasticsearch\factories\FieldMappingFactory;
use GlueAgency\Elasticsearch\models\Index;

class EntriesFieldMapper implements FieldMapperInterface
{

    public function format(Element $element, Field $field, Index $index): mixed
    {
        $related = $element->getFieldValue($field->handle)->all();

        if(empty($related)) {
            return [];
        }

        $fieldsConfig = $index->getSettings('fields');
        $extraFields = $fieldsConfig[$field->handle] ?? [];
        $factory = new FieldMappingFactory;

        $data = [];

        foreach($related as $item) {
            $mappedItem = [
                'id'    => $item->id,
                'title' => $item->title,
            ];

            // 4. Attach any requested extra fields
            foreach($extraFields as $extraHandle) {
                $extraField = Craft::$app->getFields()->getFieldByHandle($extraHandle);

                if($extraField) {
                    $mappedItem[$extraHandle] = $factory->format($item, $extraField, $index);
                } else {
                    // Fallback for native/scalar element properties
                    $mappedItem[$extraHandle] = $item->getFieldValue($extraHandle);
                }
            }

            $data[] = $mappedItem;
        }

        return $data;
    }
}
