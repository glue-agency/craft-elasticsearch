<?php

namespace GlueAgency\Elasticsearch\queue\jobs\entry;

use Craft;
use craft\elements\Entry;
use craft\queue\BaseJob;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Exception;
use GlueAgency\Elasticsearch\Elasticsearch;

class IndexEntryJob extends BaseJob
{

    public int $entryId;

    public string $indexName;

    public function execute($queue): void
    {
        $index = Elasticsearch::getInstance()->settings->getIndexByIndexName($this->indexName);

        $entry = Entry::find()
            ->status(null)
            ->id($this->entryId)
            ->site($index->site)
            ->one();

        try {
            Elasticsearch::getInstance()->documents->index($index, $entry);
        } catch(ClientResponseException|ServerResponseException $e ) {
            throw new Exception("Unable to index {$entry->id}: {$e->getMessage()}");
        }

        $this->setProgress($queue, 100);
    }

    protected function defaultDescription(): ?string
    {
        return Craft::t('elasticsearch', 'Indexing Entry #{id} to Elasticsearch', [
            'id' => $this->entryId,
        ]);
    }
}
