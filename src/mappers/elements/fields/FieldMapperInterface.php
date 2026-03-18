<?php

namespace GlueAgency\Elasticsearch\mappers\elements\fields;

use craft\base\Element;
use craft\base\Field;
use GlueAgency\Elasticsearch\models\Index;

interface FieldMapperInterface
{

    public function format(Element $element, Field $field, Index $index): mixed;
}
