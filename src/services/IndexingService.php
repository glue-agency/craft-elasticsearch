<?php

namespace GlueAgency\Elasticsearch\services;

use craft\base\Element;
use craft\helpers\ElementHelper;
use craft\helpers\Queue;
use GlueAgency\Elasticsearch\Elasticsearch;
use GlueAgency\Elasticsearch\factories\ElementQueryFilterFactory;
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

        $indexes = Elasticsearch::getInstance()->settings->getIndexesForElement($element);

        if($indexes->isNotEmpty()) {
            $filterFactory = new ElementQueryFilterFactory;

            foreach($indexes as $index) {
                if($filterFactory->shouldIndex($element, $index)) {
                    Queue::push(new IndexEntryJob([
                        'indexName' => $index->name,
                        'entryId'   => $element->id,
                    ]));
                } else {
                    Queue::push(new DeleteEntryJob([
                        'indexName' => $index->name,
                        'entryId'   => $element->id,
                    ]));
                }
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
