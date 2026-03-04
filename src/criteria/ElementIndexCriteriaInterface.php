<?php

namespace GlueAgency\Elasticsearch\criteria;

use craft\base\Element;

interface ElementIndexCriteriaInterface
{

    public static function getCriteria(Element $element): array;
}
