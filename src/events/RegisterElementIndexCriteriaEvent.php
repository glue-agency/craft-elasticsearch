<?php

namespace GlueAgency\Elasticsearch\events;

use yii\base\Event;

class RegisterElementIndexCriteriaEvent extends Event
{

    public array $builders = [];
}
