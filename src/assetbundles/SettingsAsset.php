<?php

namespace GlueAgency\Elasticsearch\assetbundles;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class SettingsAsset extends AssetBundle
{

    public function init()
    {
        $this->sourcePath = '@elasticsearch/resources/src';

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/settings.js',
        ];

        parent::init();
    }
}
