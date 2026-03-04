<?php

namespace GlueAgency\Elasticsearch\services;

use craft\elements\db\ElementQuery;
use GlueAgency\Elasticsearch\factories\ElementQueryFilterFactory;
use GlueAgency\Elasticsearch\models\Index;
use yii\base\Component;

class ElementService extends Component
{

    public function query(Index $index): ElementQuery
    {
        $query = $index->element::find()
            ->site($index->site);
//            ->status(null);

        $factory = new ElementQueryFilterFactory;
        $factory->apply($query, $index);

        return $query;
    }

    public function count(Index $index): int
    {
        return $this->query($index)->count();
    }
}
