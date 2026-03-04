<?php

namespace GlueAgency\Elasticsearch\models;

use Craft;
use craft\base\Model;
use craft\elements\Entry;
use craft\models\Site;

class Index extends Model
{

    public string $name = '';

    public string $site = '';

    public string $element = Entry::class;

    public array $settings = [];

    public function getSite(): ?Site
    {
        return Craft::$app->sites->getSiteByHandle($this->site);
    }

    public function getSettings(?string $key = null, mixed $default = []): mixed
    {
        if (is_null($key)) {
            return $this->settings;
        }

        return $this->settings[$key] ?? $default;
    }
}
