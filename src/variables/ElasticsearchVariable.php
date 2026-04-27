<?php

namespace GlueAgency\Elasticsearch\variables;

use GlueAgency\Elasticsearch\Elasticsearch;
use GlueAgency\Elasticsearch\models\Index;
use GlueAgency\Elasticsearch\models\Settings;

class ElasticsearchVariable
{
    public function settings(): Settings
    {
        return Elasticsearch::getInstance()->settings;
    }

    public function elementCount(Index $index): int
    {
        return Elasticsearch::getInstance()->elements->count($index);
    }

    public function indexExists(Index $index): bool
    {
        return Elasticsearch::getInstance()->indexes->exists($index);
    }

    public function indexCount(Index $index): int
    {
        return Elasticsearch::getInstance()->documents->count($index);
    }

    public function craftOnlyIds(Index $index): array
    {
        $craftIds   = Elasticsearch::getInstance()->elements->ids($index);
        $elasticIds = Elasticsearch::getInstance()->documents->allIds($index);

        return array_values(array_diff($craftIds, $elasticIds));
    }

    public function elasticOnlyIds(Index $index): array
    {
        $craftIds   = Elasticsearch::getInstance()->elements->ids($index);
        $elasticIds = Elasticsearch::getInstance()->documents->allIds($index);
        $expiredIds = Elasticsearch::getInstance()->elements->expiredIds($index);

        return array_values(array_diff($elasticIds, $craftIds, $expiredIds));
    }

    public function elasticExpiredIds(Index $index): array
    {
        $elasticIds = Elasticsearch::getInstance()->documents->allIds($index);
        $expiredIds = Elasticsearch::getInstance()->elements->expiredIds($index);

        return array_values(array_intersect($elasticIds, $expiredIds));
    }
}
