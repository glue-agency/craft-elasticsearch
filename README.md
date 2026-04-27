# Elasticsearch

Index Craft Elements to Elasticsearch

## Requirements

This plugin requires Craft CMS 5.0.0 or later, and PHP 8.2 or later.

## Installation

You can install this plugin from the Plugin Store or with Composer.

#### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project.test

# tell Composer to load the plugin
composer require glue-agency/craft-elasticsearch

# tell Craft to install the plugin
./craft plugin/install elasticsearch
```

## Configuration

If you create a `elasticsearch.php` file in the `config` folder you can overwrite the following plugin settings. The config file supports [multi env setup](https://craftcms.com/docs/4.x/config/#multi-environment-configs).

```php
<?php

return [
    /**
     * Connection settings
     */
    'endpoint'  => 'https://your-endpoint.cloud.es.io',
    'port'      => '443',

    /** 
     * Authentication
     * 
     * Supported auth keys: "basic", "cloudId", "apiKey"
     * 
     * Each auth key requires different settings:
     * basic: "username", "password"
     * cloudId: "cloudId"
     * apiKey: "apiKey"
     */
    'auth'      => 'apiKey',
    'apiKey'    => 'base64-key',

    /** 
     * Search
     */
    'searchKey' => 'base64-key',

    /** 
     * Indexes
     * 
     * Manage indexing and linked indexes.
     * At least sections or entryTypes must be set.
     * 
     * Available settings per index:
     *   sections   - Limit to specific section handles (optional)
     *   entryTypes - Limit to specific entry type handles (optional)
     *   statuses   - Limit to specific entry statuses (optional, defaults to ['live', 'pending'])
     *   fields     - Limit which fields are indexed, supports wildcards and nested relations (optional, defaults to all fields)
     */
    'indexes' => [
        [
            'name'       => 'index-name',
            'element'    => \craft\elements\Entry::class,
            'site'       => 'site-handle',
            'settings' => [
                'sections'   => ['section-handle-1', 'section-handle-2'],
                'entryTypes' => ['entry-type-handle-1', 'entry-type-handle-2'],
                'statuses'   => ['live', 'pending'], // default, can be omitted
            ],
        ],
        [
            'name'       => 'index-name',
            'element'    => \craft\elements\Entry::class,
            'site'       => 'site-handle',
            'settings' => [
                'sections'   => ['section-handle-3'],
                'entryTypes' => ['entry-type-handle-3'],
                'statuses'   => ['live'], // only index live entries for this index
            ],
        ],
    ],
];
```

## Extending the Plugin

You can easily add support for custom Craft Fields, or even entirely new Element Types (like Categories or Commerce Products).

### Adding Support for Custom Element Types

By default, this plugin indexes Craft `Entry` elements. To add support for another Element Type (e.g., `craft\elements\Category`), you need to register four components via Yii2 events:

1. **Criteria:** Tells the settings model how to match the element to an index (e.g. by section or entry type).
2. **Filter:** Tells the element queries how to filter elements for this index, and whether a given element should be indexed or deleted. Implement `apply()` to restrict queries, and `shouldIndex()` to gate indexing per element + index (e.g. based on configurable statuses).
3. **Schema:** Defines the Elasticsearch property mappings for the element.
4. **Mapper:** Formats the element's data before sending it to Elasticsearch.

Here is an example of how a custom module or plugin can register support for Categories:

```php
use craft\elements\Category;
use GlueAgency\Elasticsearch\factories\ElementIndexCriteriaFactory;
use GlueAgency\Elasticsearch\factories\ElementMappingFactory;
use GlueAgency\Elasticsearch\factories\ElementQueryFilterFactory;
use GlueAgency\Elasticsearch\factories\ElementSchemaFactory;
use yii\base\Event;

// Register Criteria
Event::on(
    ElementIndexCriteriaFactory::class, 
    ElementIndexCriteriaFactory::EVENT_REGISTER_CRITERIA_BUILDERS, 
    function($event) {
        $event->builders[Category::class] = MyCategoryCriteria::class;
    }
);

// Register Query Filter
Event::on(
    ElementQueryFilterFactory::class, 
    ElementQueryFilterFactory::EVENT_REGISTER_FILTERS, 
    function($event) {
        $event->filters[Category::class] = MyCategoryFilter::class;
    }
);

// Register Schema
Event::on(
    ElementSchemaFactory::class, 
    ElementSchemaFactory::EVENT_REGISTER_SCHEMAS, 
    function($event) {
        $event->schemas[Category::class] = MyCategorySchema::class;
    }
);

// Register Mapper
Event::on(
    ElementMappingFactory::class, 
    ElementMappingFactory::EVENT_REGISTER_ELEMENT_MAPPERS, 
    function($event) {
        $event->mappers[Category::class] = MyCategoryMapper::class;
    }
);
```

### Adding Support for Custom Fields

If you are using a third-party Craft field type and want to map it to a specific Elasticsearch data type, you need to register a Property Mapper (defines the Elasticsearch schema type) and a Field Mapper (formats the value).

```php
use GlueAgency\Elasticsearch\factories\FieldMappingFactory;
use GlueAgency\Elasticsearch\factories\PropertyMappingFactory;
use yii\base\Event;

// Register the Schema Property Mapper
Event::on(
    PropertyMappingFactory::class, 
    PropertyMappingFactory::EVENT_REGISTER_MAPPERS, 
    function($event) {
        $event->mappings[MyCustomField::class] = MyCustomPropertyMapper::class;
    }
);

// Register the Document Field Mapper
Event::on(
    FieldMappingFactory::class, 
    FieldMappingFactory::EVENT_REGISTER_MAPPERS, 
    function($event) {
        $event->mappings[MyCustomField::class] = MyCustomFieldMapper::class;
    }
);
```

If you do not register a field mapper for a custom field, the plugin will gracefully fall back to calling $field->serializeValue() and assigning it a standard Elasticsearch text property.
