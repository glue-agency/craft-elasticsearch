<?php

namespace GlueAgency\Elasticsearch\factories;

use craft\base\Element;
use GlueAgency\Elasticsearch\events\RegisterElementMappersEvent;
use GlueAgency\Elasticsearch\mappers\elements\ElementMapperInterface;
use GlueAgency\Elasticsearch\models\Index;
use InvalidArgumentException;
use yii\base\Arrayable;
use yii\base\Component;

class ElementMappingFactory extends Component
{

    public const EVENT_REGISTER_MAPPERS = 'registerElementMappers';

    protected array $mappers = [];

    public function init(): void
    {
        parent::init();

        $defaultMappers = [
            \craft\elements\Entry::class => \GlueAgency\Elasticsearch\mappers\elements\EntryMapper::class,
        ];

        $event = new RegisterElementMappersEvent([
            'mappers' => $defaultMappers,
        ]);

        $this->trigger(self::EVENT_REGISTER_MAPPERS, $event);

        $this->mappers = $event->mappers;
    }

    public function format(mixed $data, Index $index): array
    {
        if(is_array($data)) {
            return array_map(fn($item) => $this->format($item, $index), $data);
        }

        if($data instanceof Element) {
            foreach($this->mappers as $elementClass => $mapperClass) {
                if(is_a($data, $elementClass)) {
                    /* @var ElementMapperInterface $mapper */
                    $mapper = new $mapperClass;

                    return $mapper->format($data, $index);
                }

                throw new InvalidArgumentException('No Elasticsearch mapper registered for element class: ' . get_class($data));
            }
        }

        if($data instanceof Arrayable) {
            return $data->toArray();
        }

        throw new InvalidArgumentException('Cannot format data of type: ' . gettype($data) . '. Expected an Element or Arrayable.');
    }
}
