<?php

namespace GlueAgency\Elasticsearch\schemas\properties;

class NestedProperty extends ObjectProperty
{

    public string $type = 'nested';

    public function __construct(string $key, array $properties)
    {
        parent::__construct($key, $properties);
    }
}
