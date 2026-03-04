<?php

namespace GlueAgency\Elasticsearch\responses;

use GlueAgency\Elasticsearch\responses\parsers\DocumentParser;

class PaginatedDocumentsResponse extends BaseResponse
{

    public function getDocuments(): array
    {
        return array_map(function($document) {
            return new DocumentParser($document);
        }, $this->dataPath('hits.hits', []));
    }

    public function getMaxScore(): ?float
    {
        return $this->dataPath('hits.max_score', null);
    }
}
