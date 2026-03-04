<?php

namespace GlueAgency\Elasticsearch\schemas;

use Craft;
use GlueAgency\Elasticsearch\models\Index;
use GlueAgency\Elasticsearch\schemas\properties\BooleanProperty;
use GlueAgency\Elasticsearch\schemas\properties\DateProperty;
use GlueAgency\Elasticsearch\schemas\properties\IntegerProperty;
use GlueAgency\Elasticsearch\schemas\properties\KeywordProperty;
use GlueAgency\Elasticsearch\schemas\properties\ObjectProperty;
use GlueAgency\Elasticsearch\schemas\properties\TextProperty;

class EntrySchema extends BaseSchema implements SchemaInterface
{

    public function baseElementProperties(): void
    {
        $this->addProperty(new TextProperty('title'));
        $this->addProperty(new KeywordProperty('slug'));
        $this->addProperty(new KeywordProperty('url'));
        $this->addProperty(new BooleanProperty('enabled'));
        $this->addProperty(new ObjectProperty('site', [
            new IntegerProperty('id'),
            new KeywordProperty('language'),
        ]));
        $this->addProperty(new ObjectProperty('section', [
            new IntegerProperty('id'),
            new KeywordProperty('handle'),
        ]));
        $this->addProperty(new ObjectProperty('entry_type', [
            new IntegerProperty('id'),
            new KeywordProperty('handle'),
        ]));
        $this->addProperty(new DateProperty('post_date'));
        $this->addProperty(new DateProperty('expiry_date'));
        $this->addProperty(new DateProperty('date_updated'));
        $this->addProperty(new DateProperty('date_created'));
    }

    public function customElementProperties(Index $index): void
    {
        $sectionHandles = $index->getSettings('sections');
        $entryTypeHandles = $index->getSettings('entryTypes');

        $entryTypes = [];

        // If entryTypes are explicitly defined, map their fields
        if(! empty($entryTypeHandles)) {
            $entryTypes = array_filter(
                Craft::$app->getEntries()->getAllEntryTypes(),
                fn($type) => in_array($type->handle, $entryTypeHandles)
            );
        }
        // If sections are defined, get all entry types belonging to those sections
        else if(! empty($sectionHandles)) {
            $sections = array_filter(
                Craft::$app->getEntries()->getAllSections(),
                fn($section) => in_array($section->handle, $sectionHandles)
            );

            foreach($sections as $section) {
                $entryTypes = array_merge($entryTypes, $section->getEntryTypes());
            }
        }

        foreach($entryTypes as $model) {
            foreach($model->getFieldLayout()->getCustomFields() as $field) {
                $this->addField($field);
            }
        }
    }
}
