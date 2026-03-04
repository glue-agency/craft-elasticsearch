<?php

namespace GlueAgency\Elasticsearch\services;

use craft\helpers\App;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use GlueAgency\Elasticsearch\Elasticsearch;
use GlueAgency\Elasticsearch\models\Settings;
use yii\base\Component;

class ClientService extends Component
{

    public function get(): Client
    {
        $client = ClientBuilder::create()
            ->setHosts([
                Elasticsearch::getInstance()->settings->getEndpoint() . ':' . Elasticsearch::getInstance()->settings->getPort(),
            ]);

        if(Elasticsearch::getInstance()->settings->auth === Settings::AUTH_BASIC) {
            $client->setBasicAuthentication(Elasticsearch::getInstance()->settings->getUsername(), Elasticsearch::getInstance()->settings->getPassword());
        }

        if(Elasticsearch::getInstance()->settings->auth === Settings::AUTH_ELASTIC_CLOUD_ID) {
            $client->setElasticCloudId(Elasticsearch::getInstance()->settings->getCloudId());
        }

        if(Elasticsearch::getInstance()->settings->auth === Settings::AUTH_API_KEY) {
            $client->setApiKey(Elasticsearch::getInstance()->settings->getApiKey());
        }

        return $client->build();
    }
}
