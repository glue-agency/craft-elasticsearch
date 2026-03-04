<?php

namespace GlueAgency\Elasticsearch\services;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use GlueAgency\Elasticsearch\Elasticsearch;
use GlueAgency\Elasticsearch\factories\ElementSchemaFactory;
use GlueAgency\Elasticsearch\models\Index;
use GlueAgency\Elasticsearch\responses\parsers\IndexParser;
use stdClass;
use yii\base\Component;

class IndexService extends Component
{

    protected ?Client $client = null;

    public function init()
    {
        $this->client = Elasticsearch::getInstance()->client->get();
    }

    public function find(Index $index): ?IndexParser
    {
        try {
            $data = $this->client->indices()
                ->get([
                    'index' => $index->name
                ])
                ->asArray();

            return new IndexParser(array_merge(
                [
                    'index' => $index->name,
                ],
                $data[$index->name]
            ));
        } catch(ClientResponseException $e) {
            if($e->getResponse()->getStatusCode() === 404) {
                return null;
            }

            throw $e;
        }
    }

    public function create(Index $index): bool
    {
        $factory = new ElementSchemaFactory;

        return $this->client->indices()
            ->create([
                'index' => $index->name,
                'body' => [
                    'mappings' => $factory->build($index)
                ]
            ])
            ->asBool();
    }

    public function flush(Index $index): bool
    {
        $response = $this->client->deleteByQuery([
            'index' => $index->name,
            'body'  => [
                'query' => [
                    'match_all' => new StdClass,
                ]
            ]
        ]);

        $data = $response->asArray();

        return $data['total'] === $data['deleted'];
    }

    public function delete(Index $index): bool
    {
        return $this->client->indices()
            ->delete([
                'index' => $index->name,
            ])
            ->asBool();
    }

    public function exists(Index $index): bool
    {
        return !! $this->find($index);
    }
}
