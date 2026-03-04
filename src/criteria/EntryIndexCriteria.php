<?php

namespace GlueAgency\Elasticsearch\criteria;

use craft\base\Element;
use craft\elements\Entry;

class EntryIndexCriteria implements ElementIndexCriteriaInterface
{

    public static function getCriteria(Element $element): array
    {
        /** @var Entry $element */
        return [
            'sections'   => [$element->section->handle],
            'entryTypes' => [$element->type->handle],
        ];
    }
}
