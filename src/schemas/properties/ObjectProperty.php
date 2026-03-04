<?php

namespace GlueAgency\Elasticsearch\schemas\properties;

use Illuminate\Support\Collection;

class ObjectProperty extends BaseProperty
{

    public string $type = 'object';

    /**
     * array<BaseField> $properties
     */
    protected Collection $properties;

    public function __construct(string $key, array $properties)
    {
        parent::__construct($key);

        $this->properties = Collection::make($properties);
    }

    public function getElasticMapping(): array
    {
        return [
            'type' => $this->type,
            'properties' => $this->properties
                ->flatMap(function($property) {
                    return [$property->getKey() => $property->getElasticMapping()];
                })
                ->toArray(),
        ];
    }
}
