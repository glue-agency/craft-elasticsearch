<?php

namespace GlueAgency\Elasticsearch\models;

use craft\base\Element;
use craft\base\Model;
use craft\behaviors\EnvAttributeParserBehavior;
use craft\elements\Entry;
use craft\helpers\App;
use GlueAgency\Elasticsearch\factories\ElementIndexCriteriaFactory;
use Illuminate\Support\Collection;

class Settings extends Model
{

    const AUTH_BASIC = 'basic';
    const AUTH_ELASTIC_CLOUD_ID = 'cloudId';
    const AUTH_API_KEY = 'apiKey';

    public string $endpoint = '';

    public string $port = '9200';

    public string $auth = 'basic';

    public string $username = 'elastic';

    public string $password = '';

    public string $cloudId = '';

    public string $apiKey = '';

    public string $searchKey = '';

    public array $indexes = [];

    public function defineBehaviors(): array
    {
        return [
            'parser' => [
                'class' => EnvAttributeParserBehavior::class,
                'attributes' => [
                    'endpoint',
                    'port',
                    'auth',
                    'username',
                    'password',
                    'cloudId',
                    'apiKey',
                    'searchKey',
                ],
            ],
        ];
    }

    public function getEndpoint(): ?string
    {
        return App::parseEnv($this->endpoint);
    }

    public function getPort(): ?string
    {
        return App::parseEnv($this->port);
    }

    public function getAuth(): ?string
    {
        return App::parseEnv($this->auth);
    }

    public function getUsername(): ?string
    {
        return App::parseEnv($this->username);
    }

    public function getPassword(): ?string
    {
        return App::parseEnv($this->password);
    }

    public function getCloudId(): ?string
    {
        return App::parseEnv($this->cloudId);
    }

    public function getApiKey(): ?string
    {
        return App::parseEnv($this->apiKey);
    }

    public function getSearchKey(): ?string
    {
        return App::parseEnv($this->searchKey);
    }

    public function getIndexes(): Collection
    {
        return Collection::make($this->indexes)->mapInto(Index::class);
    }

    public function getIndexByIndexName($name): ?Index
    {
        $config = array_find($this->indexes, function($index) use ($name) {
            return $index['name'] === $name;
        });

        if($config) {
            return new Index($config);
        }

        return null;
    }

    public function getIndexFor(array $criteria): ?Index
    {
        return $this->getIndexesFor($criteria)?->first();
    }

    public function getIndexesFor(array $criteria): Collection
    {
        return Collection::make($this->indexes)
            ->mapInto(Index::class)

            // Match Site
            ->when(isset($criteria['site']), function (Collection $collection) use ($criteria) {
                $expectedSite = is_object($criteria['site']) ? $criteria['site']->handle : $criteria['site'];

                return $collection->filter(fn(Index $index) => $index->site === $expectedSite);
            })

            // Match Element
            ->when(isset($criteria['element']), function (Collection $collection) use ($criteria) {
                return $collection->filter(fn(Index $index) => $index->element === $criteria['element']);
            })

            // Match Custom Settings
            ->when(isset($criteria['settings']), function (Collection $collection) use ($criteria) {
                return $collection->filter(function (Index $index) use ($criteria) {
                    foreach ($criteria['settings'] as $key => $expectedValues) {
                        $expectedValues = is_array($expectedValues) ? $expectedValues : [$expectedValues];
                        $expectedValues = array_map(fn($item) => is_object($item) ? $item->handle : $item, $expectedValues);

                        // Fetch this specific setting from the Index model
                        $indexValues = $index->getSettings($key);

                        // If the index has this setting configured, ensure our element matches
                        if (!empty($indexValues) && count(array_intersect($indexValues, $expectedValues)) === 0) {
                            return false;
                        }
                    }
                    return true;
                });
            });
    }

    public function getIndexesForElement(Element $element): Collection
    {
        $factory = new ElementIndexCriteriaFactory;
        $criteria = $factory->build($element);

        return $this->getIndexesFor($criteria);
    }

    public function defineRules(): array
    {
        $rules = [];

        $rules[] = [['endpoint', 'port', 'auth', 'searchKey'], 'required'];

        return $rules;
    }
}
