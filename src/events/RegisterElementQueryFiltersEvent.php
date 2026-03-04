<?php

namespace GlueAgency\Elasticsearch\events;

use yii\base\Event;

class RegisterElementQueryFiltersEvent extends Event
{

    public array $filters = [];
}
