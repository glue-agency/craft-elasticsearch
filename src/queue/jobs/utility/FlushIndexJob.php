<?php

namespace GlueAgency\Elasticsearch\queue\jobs\utility;

use Craft;
use craft\queue\BaseJob;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use GlueAgency\Elasticsearch\Elasticsearch;

class FlushIndexJob extends BaseJob
{

    public string $index;

    public function execute($queue): void
    {
        $index = Elasticsearch::getInstance()->settings->getIndexByIndexName($this->index);

        try {
            Elasticsearch::getInstance()->indexes->flush($index);
        } catch(ClientResponseException|ServerResponseException $e ) {
            throw new \Exception("Could not flush index: {$e->getMessage()}");
        }

        $this->setProgress($queue, 100);
    }

    protected function defaultDescription(): ?string
    {
        $index = Elasticsearch::getInstance()->settings->getIndexByIndexName($this->index);

        return Craft::t('elasticsearch', 'Flushing index \'{name}\'', [
            'name' => $index->name,
        ]);
    }
}
