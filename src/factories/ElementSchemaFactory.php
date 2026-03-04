<?php

namespace GlueAgency\Elasticsearch\factories;

use GlueAgency\Elasticsearch\events\RegisterElementSchemasEvent;
use GlueAgency\Elasticsearch\models\Index;
use GlueAgency\Elasticsearch\schemas\SchemaInterface;
use InvalidArgumentException;
use yii\base\Component;

class ElementSchemaFactory extends Component
{

    public const EVENT_REGISTER_SCHEMAS = 'registerElementSchemas';

    protected array $schemas = [];

    public function init(): void
    {
        parent::init();

        $defaultSchemas = [
            \craft\elements\Entry::class => \GlueAgency\Elasticsearch\schemas\EntrySchema::class,
        ];

        $event = new RegisterElementSchemasEvent([
            'schemas' => $defaultSchemas,
        ]);

        $this->trigger(self::EVENT_REGISTER_SCHEMAS, $event);

        $this->schemas = $event->schemas;
    }

    public function build(Index $index): array
    {
        $element = $index->element;

        if (isset($this->schemas[$element])) {
            /* @var SchemaInterface $schema */
            $schema = new $this->schemas[$element];

            return $schema->build($index);
        }

        throw new InvalidArgumentException("No Elasticsearch schema registered for element class: {$element}");
    }
}
