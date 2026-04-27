<?php

namespace GlueAgency\Elasticsearch\filters;

use craft\base\Element;
use craft\elements\db\ElementQueryInterface;
use GlueAgency\Elasticsearch\models\Index;

interface ElementQueryFilterInterface
{

    public static function apply(ElementQueryInterface $query, Index $index): void;

    public static function shouldIndex(Element $element, Index $index): bool;
}
