<?php

namespace GlueAgency\Elasticsearch\mappers\elements\fields;

use craft\base\Element;
use craft\base\Field;
use GlueAgency\Elasticsearch\models\Index;

class DateFieldMapper implements FieldMapperInterface
{

    public function format(Element $element, Field $field, Index $index): mixed
    {
        $value = $element->getFieldValue($field->handle);

        if($value) {
            if($field->showDate && $field->showTime){
                return $value->format('c');
            }

            if($field->showDate) {
                return $value->format('Y-m-d');
            }

            if($field->showTime) {
                return $value->format('H:i:s');
            }
        }

        return null;
    }
}
