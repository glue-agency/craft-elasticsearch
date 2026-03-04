<?php

namespace GlueAgency\Elasticsearch\mappers\properties;

use craft\base\Field;
use GlueAgency\Elasticsearch\schemas\properties\BaseProperty;
use GlueAgency\Elasticsearch\schemas\properties\IntegerProperty;
use GlueAgency\Elasticsearch\schemas\properties\NestedProperty;
use GlueAgency\Elasticsearch\schemas\properties\ObjectProperty;
use GlueAgency\Elasticsearch\schemas\properties\TextProperty;

class EntriesPropertyMapper implements PropertyMapperInterface
{
    public function map(Field $field, string $handle): BaseProperty
    {
        $properties = [
            new IntegerProperty('id'),
            new TextProperty('title'),
        ];

        // If maxRelations is exactly 1, an Object is sufficient.
        // Otherwise, use a Nested property to array-ify the relations safely.
        if (property_exists($field, 'maxRelations') && $field->maxRelations === 1) {
            return new ObjectProperty($handle, $properties);
        }

        return new NestedProperty($handle, $properties);
    }
}
