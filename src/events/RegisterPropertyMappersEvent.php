<?php

namespace GlueAgency\Elasticsearch\events;

use yii\base\Event;

class RegisterPropertyMappersEvent extends Event
{

    public array $mappings = [];
}
