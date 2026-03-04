<?php

namespace GlueAgency\Elasticsearch\responses;

class BulkIndexResponse extends BaseResponse
{

    public function getErrors(): array
    {
        return array_map(function($error) {
            dd($error);

            return [

            ];
        }, $this->getItems());
    }

    public function hasItems(): bool
    {
        return ! ! count($this->response['items']);
    }

    public function getItems(): array
    {
        return $this->response['items'];
    }
}
