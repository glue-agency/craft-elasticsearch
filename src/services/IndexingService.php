<?php

namespace GlueAgency\Elasticsearch\services;

use craft\base\Element;
use craft\helpers\ElementHelper;
use craft\helpers\Queue;
use GlueAgency\Elasticsearch\Elasticsearch;
use GlueAgency\Elasticsearch\queue\jobs\entry\DeleteEntryJob;
use GlueAgency\Elasticsearch\queue\jobs\entry\IndexEntryJob;
use yii\base\Component;

class IndexingService extends Component
{

    public function index(Element $element): void
    {
        if(ElementHelper::isDraftOrRevision($element)) {
            return;
        }

        if($element->propagating) {
            return;
        }

        if(empty($element->getDirtyAttributes()) && empty($element->getDirtyFields())) {
            return;
        }

        if($element->getStatus() === Element::STATUS_DISABLED) {
            $this->delete($element);

            return;
        }

        $indexes = Elasticsearch::getInstance()->settings->getIndexesForElement($element);

        if($indexes->isNotEmpty()) {
            foreach($indexes as $index) {
                Queue::push(new IndexEntryJob([
                    'indexName' => $index->name,
                    'entryId'   => $element->id,
                ]));
            }
        }
    }

    public function delete(Element $element): void
    {
        if(ElementHelper::isDraftOrRevision($element)) {
            return;
        }

        $indexes = Elasticsearch::getInstance()->settings->getIndexesForElement($element);

        if($indexes->isNotEmpty()) {
            foreach($indexes as $index) {
                Queue::push(new DeleteEntryJob([
                    'indexName' => $index->name,
                    'entryId'   => $element->id,
                ]));
            }
        }
    }
}
