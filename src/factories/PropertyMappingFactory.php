<?php

namespace GlueAgency\Elasticsearch\factories;

use craft\base\Field;
use GlueAgency\Elasticsearch\events\RegisterPropertyMappersEvent;
use GlueAgency\Elasticsearch\mappers\properties\PropertyMapperInterface;
use GlueAgency\Elasticsearch\models\Index;
use GlueAgency\Elasticsearch\schemas\properties\BaseProperty;
use GlueAgency\Elasticsearch\schemas\properties\TextProperty;
use yii\base\Component;

class PropertyMappingFactory extends Component
{

    public const EVENT_REGISTER_MAPPERS = 'registerPropertyMappers';

    protected array $mappings = [];

    public function init(): void
    {
        parent::init();

        $defaultMappings = [
            \craft\fields\Entries::class     => \GlueAgency\Elasticsearch\mappers\properties\EntriesPropertyMapper::class,
            \craft\fields\Number::class      => \GlueAgency\Elasticsearch\mappers\properties\NumberPropertyMapper::class,
            \craft\fields\Date::class        => \GlueAgency\Elasticsearch\mappers\properties\DatePropertyMapper::class,
            \craft\fields\Lightswitch::class => \GlueAgency\Elasticsearch\mappers\properties\LightswitchPropertyMapper::class,

            // Use strings for third-party plugins to avoid fatal errors if they aren't installed
            'craft\redactor\Field'           => \GlueAgency\Elasticsearch\mappers\properties\RichTextPropertyMapper::class,
            'craft\ckeditor\Field'           => \GlueAgency\Elasticsearch\mappers\properties\RichTextPropertyMapper::class,
        ];

        $event = new RegisterPropertyMappersEvent([
            'mappings' => $defaultMappings,
        ]);

        $this->trigger(self::EVENT_REGISTER_MAPPERS, $event);

        $this->mappings = $event->mappings;
    }

    public function create(Field $field, string $handle, Index $index): BaseProperty
    {
        foreach($this->mappings as $fieldClass => $mapperClass) {
            if(is_a($field, $fieldClass)) {
                /** @var PropertyMapperInterface $mapper */
                $mapper = new $mapperClass;

                return $mapper->map($field, $handle, $index);
            }
        }

        // Default fallback for any unknown or standard text fields
        return new TextProperty($handle);
    }
}
