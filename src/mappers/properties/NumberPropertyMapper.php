<?php

namespace GlueAgency\Elasticsearch\mappers\properties;

use craft\base\Field;
use GlueAgency\Elasticsearch\schemas\properties\BaseProperty;
use GlueAgency\Elasticsearch\schemas\properties\FloatProperty;
use GlueAgency\Elasticsearch\schemas\properties\IntegerProperty;

class NumberPropertyMapper implements PropertyMapperInterface
{

    public function map(Field $field, string $handle): BaseProperty
    {
        if(property_exists($field, 'decimals') && $field->decimals <= 0) {
            return new IntegerProperty($handle);
        }

        return new FloatProperty($handle);
    }
}
