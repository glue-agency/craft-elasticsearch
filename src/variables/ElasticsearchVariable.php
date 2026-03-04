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
}
