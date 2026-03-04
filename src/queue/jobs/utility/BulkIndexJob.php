<?php

namespace GlueAgency\Elasticsearch\queue\jobs\utility;

use Craft;
use craft\helpers\Db;
use craft\queue\BaseJob;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Exception;
use GlueAgency\Elasticsearch\Elasticsearch;

class BulkIndexJob extends BaseJob
{

    public string $index;

    protected int $batchSize = 50;

    protected int $offset = 0;

    public function execute($queue): void
    {
        $index = Elasticsearch::getInstance()->settings->getIndexByIndexName($this->index);
        $elementQuery = Elasticsearch::getInstance()->elements->query($index);
        $elementCount = $elementQuery->count();

        foreach(Db::batch($elementQuery, $this->batchSize) as $batch) {
            try {
                $response = Elasticsearch::getInstance()->documents->bulkIndex($index, $batch);

                if($response->hasErrors()) {
                    throw new Exception("Bulk index failed: {$response->getErrors()}");
                }
            } catch(ClientResponseException|ServerResponseException $e) {
                throw new Exception("Bulk index failed: {$e->getMessage()}");
            }

            $this->offset += $this->batchSize;
            $this->setProgress($queue, $this->offset / $elementCount);
        }
    }

    protected function defaultDescription(): ?string
    {
        $index = Elasticsearch::getInstance()->settings->getIndexByIndexName($this->index);

        return Craft::t('elasticsearch', 'Indexing Entries to \'{index}\'', [
            'index' => $index->name,
        ]);
    }
}
