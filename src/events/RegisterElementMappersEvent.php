<?php

namespace GlueAgency\Elasticsearch\events;

use yii\base\Event;

class RegisterElementMappersEvent extends Event
{

    public array $mappers = [];
}
