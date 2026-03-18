<?php

namespace GlueAgency\Elasticsearch\criteria;

use craft\base\Element;
use craft\elements\Entry;

class EntryIndexCriteria implements ElementIndexCriteriaInterface
{

    /**
     * @var Entry $element
     */
    public static function getCriteria(Element $element): array
    {
        $sections = [];
        $entryTypes = [];

        if($element->section) {
            $sections[] = $element->section->handle;
        }

        if($element->type) {
            $entryTypes[] = $element->type->handle;
        }

        return [
            'sections'   => $sections,
            'entryTypes' => $entryTypes,
        ];
    }
}
