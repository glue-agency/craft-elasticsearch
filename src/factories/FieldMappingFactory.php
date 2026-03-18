<?php

namespace GlueAgency\Elasticsearch\factories;

use craft\base\Element;
use craft\base\Field;
use GlueAgency\Elasticsearch\events\RegisterFieldMappersEvent;
use GlueAgency\Elasticsearch\mappers\elements\fields\FieldMapperInterface;
use GlueAgency\Elasticsearch\models\Index;
use yii\base\Component;

class FieldMappingFactory extends Component
{

    public const EVENT_REGISTER_MAPPERS = 'registerFieldMappers';

    protected array $mappings = [];

    public function init(): void
    {
        parent::init();

        $defaultMappings = [
            \craft\fields\Entries::class => \GlueAgency\Elasticsearch\mappers\elements\fields\EntriesFieldMapper::class,
            \craft\fields\Assets::class  => \GlueAgency\Elasticsearch\mappers\elements\fields\AssetsFieldMapper::class,
            \craft\fields\Matrix::class  => \GlueAgency\Elasticsearch\mappers\elements\fields\MatrixFieldMapper::class,
            \craft\fields\Date::class    => \GlueAgency\Elasticsearch\mappers\elements\fields\DateFieldMapper::class,
        ];

        $event = new RegisterFieldMappersEvent([
            'mappings' => $defaultMappings,
        ]);

        $this->trigger(self::EVENT_REGISTER_MAPPERS, $event);

        $this->mappings = $event->mappings;
    }

    public function format(Element $element, Field $field, Index $index): mixed
    {
        foreach ($this->mappings as $fieldClass => $mapperClass) {
            if (is_a($field, $fieldClass)) {
                /* @var FieldMapperInterface $mapper */
                $mapper = new $mapperClass;

                return $mapper->format($element, $field, $index);
            }
        }

        return $field->serializeValue($element->getFieldValue($field->handle), $element);
    }
}
