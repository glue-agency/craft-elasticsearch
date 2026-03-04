<?php

namespace GlueAgency\Elasticsearch\schemas\properties;

use craft\base\Field;

interface PropertyMapperInterface
{

    public function map(Field $field, string $handle): BaseProperty;
}
