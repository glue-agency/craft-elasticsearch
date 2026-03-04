<?php

namespace GlueAgency\Elasticsearch\events;

use yii\base\Event;

class RegisterFieldMappersEvent extends Event
{

    public array $mappings = [];
}
