<?php

namespace GlueAgency\Elasticsearch\mappers\properties;

use craft\base\Field;
use GlueAgency\Elasticsearch\models\Index;
use GlueAgency\Elasticsearch\schemas\properties\BaseProperty;
use GlueAgency\Elasticsearch\schemas\properties\DateProperty;

class DatePropertyMapper implements PropertyMapperInterface
{

    public function map(Field $field, string $handle, Index $index): BaseProperty
    {
        return new DateProperty($handle);
    }
}
