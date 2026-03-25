<?php

namespace GlueAgency\Elasticsearch\factories;

use craft\base\Element;
use GlueAgency\Elasticsearch\events\RegisterElementIndexCriteriaEvent;
use yii\base\Component;

class ElementIndexCriteriaFactory extends Component
{

    public const EVENT_REGISTER_CRITERIA_BUILDERS = 'registerCriteriaBuilders';

    protected array $builders = [];

    public function init(): void
    {
        parent::init();

        $defaultBuilders = [
            \craft\elements\Entry::class => \GlueAgency\Elasticsearch\criteria\EntryIndexCriteria::class,
        ];

        $event = new RegisterElementIndexCriteriaEvent([
            'builders' => $defaultBuilders,
        ]);

        $this->trigger(self::EVENT_REGISTER_CRITERIA_BUILDERS, $event);

        $this->builders = $event->builders;
    }

    public function build(Element $element): array
    {
        $criteria = [
            'site'     => $element->site->handle,
            'element'  => get_class($element),
            'settings' => [],
        ];

        foreach($this->builders as $elementClass => $builderClass) {
            if(is_a($element, $elementClass)) {
                $criteria['settings'] = array_merge(
                    $criteria['settings'],
                    $builderClass::getCriteria($element)
                );
            }
        }

        return $criteria;
    }
}
