<?php

namespace GlueAgency\Elasticsearch\filters;

use craft\elements\db\ElementQueryInterface;
use GlueAgency\Elasticsearch\models\Index;

class EntryQueryFilter implements ElementQueryFilterInterface
{

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
    }
}
