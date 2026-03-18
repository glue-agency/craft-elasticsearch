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
        if(is_null($key)) {
            return $this->settings;
        }

        return $this->settings[$key] ?? $default;
    }

    public function shouldIndexField(string $handle): bool
    {
        $fields = $this->getSettings('fields', ['*']);

        // Wildcard allows all fields
        if(in_array('*', $fields)) {
            return true;
        }

        // Listed as a flat value (e.g. ['body', 'summary'])
        if(in_array($handle, $fields)) {
            return true;
        }

        // Listed as a key for nested relations (e.g. ['relatedAuthors' => ['bio']])
        if(array_key_exists($handle, $fields)) {
            return true;
        }

        return false;
    }
}
