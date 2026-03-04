<?php

namespace GlueAgency\Elasticsearch\assetbundles;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class ElasticsearchAsset extends AssetBundle
{

    public function init()
    {
        $this->sourcePath = '@elasticsearch/resources/src';

        $this->depends = [
            CpAsset::class,
        ];

        $this->css = [
            'css/elasticsearch.css',
        ];

        parent::init();
    }
}
