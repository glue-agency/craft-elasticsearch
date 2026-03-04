<?php

namespace GlueAgency\Elasticsearch\queue\jobs\utility;

use Craft;
use craft\queue\BaseJob;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use GlueAgency\Elasticsearch\Elasticsearch;

class UpdateMappingJob extends BaseJob
{

    public string $index;

    public function execute($queue): void
    {
        $index = Elasticsearch::getInstance()->getSettings()->getIndexByIndexName($this->index);

        try {
            Elasticsearch::getInstance()->mapping->update($index);
        } catch(ClientResponseException|ServerResponseException $e ) {
            throw new \Exception("Could not update mapping: {$e->getMessage()}");
        }

        $this->setProgress($queue, 100);
    }

    protected function defaultDescription(): ?string
    {
        $index = Elasticsearch::getInstance()->getSettings()->getIndexByIndexName($this->index);

        return Craft::t('elasticsearch', 'Updating index \'{index}\' mappings', [
            'index' => $index->name,
        ]);
    }
}
