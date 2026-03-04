<?php

namespace GlueAgency\Elasticsearch\responses\parsers;

use Cake\Utility\Hash;

abstract class BaseParser
{
    protected array $_data = [];

    public function __construct(array $data)
    {
        $this->_data = $data;
    }

    public function getRawData(): array
    {
        return $this->_data;
    }

    protected function dataPath(string $path, mixed $default = null): mixed
    {
        return Hash::get($this->_data, $path, $default);
    }

    public function __get(string $name)
    {
        if(method_exists($this, $name)) {
            return $this->{$name}();
        }

        return $this->dataPath("_source.{$name}");
    }
}
