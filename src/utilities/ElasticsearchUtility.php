<?php

namespace GlueAgency\Elasticsearch\utilities;

use Craft;
use craft\base\Utility;
use GlueAgency\Elasticsearch\Elasticsearch;

class ElasticsearchUtility extends Utility
{

    public static function displayName(): string
    {
        return Craft::t('elasticsearch', 'Elasticsearch');
    }

    public static function id(): string
    {
        return 'elasticsearch';
    }

    public static function icon(): ?string
    {
        return Elasticsearch::getInstance()->getBasePath() . DIRECTORY_SEPARATOR . 'icon-mask.svg';
    }

    public static function contentHtml(): string
    {
        return Craft::$app->getView()->renderTemplate('elasticsearch/utility/index', [
            'indexes'      => Elasticsearch::getInstance()->settings->getIndexes(),
            'entryTypes'   => Craft::$app->getEntries()->getAllEntryTypes(),
        ]);
    }
}
