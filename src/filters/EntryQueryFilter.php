<?php

namespace GlueAgency\Elasticsearch\filters;

use craft\base\Element;
use craft\elements\Entry;
use craft\elements\db\ElementQueryInterface;
use GlueAgency\Elasticsearch\models\Index;

class EntryQueryFilter implements ElementQueryFilterInterface
{

    protected static $defaultStatuses = [
        Entry::STATUS_LIVE,
        Entry::STATUS_PENDING
    ];

    public static function apply(ElementQueryInterface $query, Index $index): void
    {
        $sections = $index->getSettings('sections');

        if(! empty($sections)) {
            $query->section($sections);
        }

        $entryTypes = $index->getSettings('entryTypes');

        if(! empty($entryTypes)) {
            $query->type($entryTypes);
        }

        $query->status($index->getSettings('statuses', self::$defaultStatuses));
    }

    public static function shouldIndex(Element $element, Index $index): bool
    {
        $statuses = $index->getSettings('statuses', self::$defaultStatuses);

        return in_array($element->getStatus(), $statuses);
    }
}
