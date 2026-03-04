<?php

namespace GlueAgency\Elasticsearch\mappers\properties;

use craft\base\Field;
use GlueAgency\Elasticsearch\schemas\properties\BaseProperty;

interface PropertyMapperInterface
{

    public function map(Field $field, string $handle): BaseProperty;
}
