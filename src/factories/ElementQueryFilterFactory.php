<?php

namespace GlueAgency\Elasticsearch\factories;

use craft\elements\db\ElementQueryInterface;
use GlueAgency\Elasticsearch\events\RegisterElementQueryFiltersEvent;
use GlueAgency\Elasticsearch\models\Index;
use yii\base\Component;

class ElementQueryFilterFactory extends Component
{

    public const EVENT_REGISTER_FILTERS = 'registerElementQueryFilters';

    protected array $filters = [];

    public function init(): void
    {
        parent::init();

        $defaultFilters = [
            \craft\elements\Entry::class => \GlueAgency\Elasticsearch\filters\EntryQueryFilter::class,
        ];

        $event = new RegisterElementQueryFiltersEvent([
            'filters' => $defaultFilters,
        ]);

        $this->trigger(self::EVENT_REGISTER_FILTERS, $event);

        $this->filters = $event->filters;
    }

    /**
     * Apply the appropriate query filters for the given Index's element type.
     */
    public function apply(ElementQueryInterface $query, Index $index): void
    {
        $element = $index->element;

        if(isset($this->filters[$element])) {
            $filterClass = $this->filters[$element];
            $filterClass::apply($query, $index);
        }
    }
}
