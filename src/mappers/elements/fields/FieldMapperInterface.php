<?php

namespace GlueAgency\Elasticsearch\mappers\elements\fields;

use craft\base\Element;
use craft\base\Field;

interface FieldMapperInterface
{

    public function format(Element $element, Field $field): mixed;
}
