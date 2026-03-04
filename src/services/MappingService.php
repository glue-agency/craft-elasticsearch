<?php

namespace GlueAgency\Elasticsearch\services;

use Elastic\Elasticsearch\Client;
use GlueAgency\Elasticsearch\Elasticsearch;
use GlueAgency\Elasticsearch\factories\ElementSchemaFactory;
use GlueAgency\Elasticsearch\models\Index;
use yii\base\Component;

class MappingService extends Component
{

    protected ?Client $client = null;

    public function init()
    {
        $this->client = Elasticsearch::getInstance()->client->get();
    }

    public function update(Index $index): bool
    {
        $factory = new ElementSchemaFactory;

        return $this->client->indexes()
            ->putMapping([
                'index' => $index->name,
                'body'  => $factory->build($index),
            ])
            ->asBool();
    }
}
