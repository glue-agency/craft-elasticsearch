<?php

namespace GlueAgency\Elasticsearch\events;

use yii\base\Event;

class RegisterElementSchemasEvent extends Event
{

    public array $schemas = [];
}
