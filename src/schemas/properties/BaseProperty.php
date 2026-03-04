<?php

namespace GlueAgency\Elasticsearch\schemas\properties;

abstract class BaseProperty
{

    public string $key;

    public string $type = 'text';

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getElasticMapping(): array
    {
        return [
            'type' => $this->type,
        ];
    }
}
