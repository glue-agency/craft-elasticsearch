<?php

namespace GlueAgency\Elasticsearch\schemas;

use craft\base\Field;
use GlueAgency\Elasticsearch\factories\PropertyMappingFactory;
use GlueAgency\Elasticsearch\helpers\FieldHelper;
use GlueAgency\Elasticsearch\models\Index;
use GlueAgency\Elasticsearch\schemas\properties\BaseProperty;
use Illuminate\Support\Collection;

abstract class BaseSchema
{

    protected array $schema = [];

    protected Collection $properties;

    protected PropertyMappingFactory $propertyFactory;

    public function __construct()
    {
        $this->properties = new Collection;
        $this->propertyFactory = new PropertyMappingFactory;
    }

    public function build(Index $index): array
    {
        $this->baseElementProperties();
        $this->customElementProperties($index);

        return [
            'properties' => $this->properties
                ->flatMap(function(BaseProperty $property) {
                    return [$property->getKey() => $property->getElasticMapping()];
                })
                ->unique(function($item, $key) {
                    return $key;
                })
                ->toArray(),
        ];
    }

    abstract public function baseElementProperties(): void;

    abstract public function customElementProperties(Index $index): void;

    public function addProperty(BaseProperty $property): void
    {
        $this->properties->push($property);
    }

    public function addField(Field $field, Index $index): void
    {
        $this->addProperty(
            $this->propertyFactory->create($field, FieldHelper::toElasticSafeName($field), $index)
        );
    }
}
