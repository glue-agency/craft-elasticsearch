<?php

namespace GlueAgency\Elasticsearch\services;

use craft\base\Element;
use craft\elements\Entry;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use GlueAgency\Elasticsearch\Elasticsearch;
use GlueAgency\Elasticsearch\factories\ElementMappingFactory;
use GlueAgency\Elasticsearch\models\Index;
use GlueAgency\Elasticsearch\responses\BulkIndexResponse;
use GlueAgency\Elasticsearch\responses\PaginatedDocumentsResponse;
use GlueAgency\Elasticsearch\responses\parsers\DocumentParser;
use yii\base\Component;

class DocumentService extends Component
{

    protected ?Client $client = null;

    public function init()
    {
        $this->client = Elasticsearch::getInstance()->client->get();
    }

    public function all(Index $index): PaginatedDocumentsResponse
    {
        $data = $this->client->search([
                'index' => $index->name,
            ])
            ->asArray();

        return new PaginatedDocumentsResponse($data);
    }

    public function count(Index $index): int
    {
        $data = $this->client->count([
                'index' => $index->name,
            ])
            ->asArray();

        return $data['count'];
    }

    public function findById(Index $index, int $id): ?DocumentParser
    {
        try {
            $data = $this->client->get([
                    'index' => $index->name,
                    'id' => $id,
                ])
                ->asArray();

            return new DocumentParser($data);
        } catch(ClientResponseException $e) {
            if($e->getResponse()->getStatusCode() === 404) {
                return null;
            }

            throw $e;
        }
    }

    public function index(Index $index, Element $element): bool
    {
        return $this->client
            ->index([
                'index' => $index->name,
                'id'    => $element->id,
                'body'  => $this->formatData($element, $index),
            ])
            ->asBool();
    }

    public function bulkIndex(Index $index, array $elements): BulkIndexResponse
    {
        $data = [];

        foreach($elements as $element) {
            $data[] = [
                'index' => [
                    '_index' => $index->name,
                    '_id' => $element->id,
                ]
            ];
            $data[] = $this->formatData($element, $index);
        }

        $response = $this->client->bulk([
            'body' => $data
        ]);

        return new BulkIndexResponse($response->asArray());
    }

    public function deleteById(Index $index, int $id): bool
    {
        return $this->client->delete([
                'index' => $index->name,
                'id' => $id,
            ])
            ->asBool();
    }

    public function delete(Index $index, Entry $entry): bool
    {
        return $this->deleteById($index, $entry->id);
    }

    protected function formatData(mixed $data, Index $index): array
    {
        $factory = new ElementMappingFactory;

        return $factory->format($data, $index);
    }
}
