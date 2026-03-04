<?php

namespace GlueAgency\Elasticsearch\mappers\elements;

use craft\base\Element;

interface ElementMapperInterface
{

    public function format(Element $element): mixed;
}
