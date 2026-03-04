<?php

namespace GlueAgency\Elasticsearch\schemas\properties;

class TextProperty extends BaseProperty
{

    public string $type = 'text';

    protected bool $withKeyword = true;

    public function __construct(string $key, bool $withKeyword = true)
    {
        parent::__construct($key);

        $this->withKeyword = $withKeyword;
    }

    public function getElasticMapping(): array
    {
        $mapping = parent::getElasticMapping();

        if($this->withKeyword) {
            $mapping['fields'] = [
                'keyword' => [
                    'type' => 'keyword',
                ],
            ];
        }

        return $mapping;
    }
}
