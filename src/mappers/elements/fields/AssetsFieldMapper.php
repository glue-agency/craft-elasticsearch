<?php

namespace GlueAgency\Elasticsearch\mappers\elements\fields;

use craft\base\Element;
use craft\base\Field;
use GlueAgency\Elasticsearch\models\Index;

class AssetsFieldMapper implements FieldMapperInterface
{

    public function format(Element $element, Field $field, Index $index): mixed
    {
        $related = $element->getFieldValue($field->handle)->all();

        return array_column($related, 'title');
    }
}
