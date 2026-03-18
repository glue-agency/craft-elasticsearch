<?php

namespace GlueAgency\Elasticsearch\mappers\properties;

use Craft;
use craft\base\Field;
use GlueAgency\Elasticsearch\factories\PropertyMappingFactory;
use GlueAgency\Elasticsearch\models\Index;
use GlueAgency\Elasticsearch\schemas\properties\BaseProperty;
use GlueAgency\Elasticsearch\schemas\properties\IntegerProperty;
use GlueAgency\Elasticsearch\schemas\properties\NestedProperty;
use GlueAgency\Elasticsearch\schemas\properties\ObjectProperty;
use GlueAgency\Elasticsearch\schemas\properties\TextProperty;

class EntriesPropertyMapper implements PropertyMapperInterface
{

    public function map(Field $field, string $handle, Index $index): BaseProperty
    {
        $properties = [
            new IntegerProperty('id'),
            new TextProperty('title'),
        ];

        // Look for explicit relational expansions in the fields config
        $fieldsConfig = $index->getSettings('fields');
        $extraFields = $fieldsConfig[$field->handle] ?? [];

        if(is_array($extraFields)) {
            $factory = new PropertyMappingFactory;

            foreach($extraFields as $extraHandle) {
                $extraFieldModel = Craft::$app->getFields()->getFieldByHandle($extraHandle);

                if($extraFieldModel) {
                    $properties[] = $factory->create($extraFieldModel, $extraHandle, $index);
                }
            }
        }

        // If maxRelations is exactly 1, an Object is sufficient.
        // Otherwise, use a Nested property to array-ify the relations safely.
        if(property_exists($field, 'maxRelations') && $field->maxRelations === 1) {
            return new ObjectProperty($handle, $properties);
        }

        return new NestedProperty($handle, $properties);
    }
}
