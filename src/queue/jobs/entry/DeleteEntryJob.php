<?php

namespace GlueAgency\Elasticsearch\queue\jobs\entry;

use Craft;
use craft\queue\BaseJob;
use GlueAgency\Elasticsearch\Elasticsearch;

class DeleteEntryJob extends BaseJob
{

    public int $entryId;

    public string $indexName;

    public function execute($queue): void
    {
        $index = Elasticsearch::getInstance()->settings->getIndexByIndexName($this->indexName);

        Elasticsearch::getInstance()->documents->deleteById($index, $this->entryId);

        $this->setProgress($queue, 100);
    }

    protected function defaultDescription(): ?string
    {
        return Craft::t('elasticsearch', 'Deleting Entry #{id} from Elasticsearch', [
            'id' => $this->entryId,
        ]);
    }
}
