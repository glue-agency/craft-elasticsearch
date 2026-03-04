<?php

namespace GlueAgency\Elasticsearch\responses;

use Cake\Utility\Hash;

abstract class BaseResponse
{

    protected array $_data = [];

    public function __construct(array $data)
    {
        $this->_data = $data;
    }

    public function getTotal(): int
    {
        return $this->dataPath('hits.total.value', 0);
    }

    public function hasErrors(): bool
    {
        return !! $this->dataPath('errors', false);
    }

    public function getRawData(): array
    {
        return $this->_data;
    }

    protected function dataPath(string $path, mixed $default = null): mixed
    {
        return Hash::get($this->_data, $path, $default);
    }
}
