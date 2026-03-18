<?php

namespace GlueAgency\Elasticsearch\mappers\elements;

use craft\base\Element;
use GlueAgency\Elasticsearch\models\Index;

interface ElementMapperInterface
{

    public function format(Element $element, Index $index): mixed;
}
