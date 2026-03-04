<?php

namespace GlueAgency\Elasticsearch\responses\parsers;

use Craft;
use craft\models\EntryType;
use craft\models\Section;
use craft\models\Site;

class DocumentParser extends BaseParser
{

    public function id()
    {
        return $this->dataPath('_id');
    }

    public function site(): ?Site
    {
        return Craft::$app->getSites()->getSiteById(
            $this->dataPath('_source.site.id')
        );
    }

    public function section(): ?Section
    {
        return Craft::$app->getEntries()->getSectionById(
            $this->dataPath('_source.section.id')
        );
    }

    public function entryType(): ?EntryType
    {
        return Craft::$app->getEntries()->getEntryTypeById(
            $this->dataPath('_source.entry_type.id')
        );
    }
}
