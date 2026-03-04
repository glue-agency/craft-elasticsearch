<?php

namespace GlueAgency\Elasticsearch\controllers;

use Craft;
use craft\helpers\Queue;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use GlueAgency\Elasticsearch\Elasticsearch;
use GlueAgency\Elasticsearch\queue\jobs\utility\BulkIndexJob;
use GlueAgency\Elasticsearch\queue\jobs\utility\FlushIndexJob;
use GlueAgency\Elasticsearch\queue\jobs\utility\UpdateMappingJob;
use yii\web\Response;

class UtilitiesController extends Controller
{

    public function actionIndex(): Response
    {
        $this->requirePostRequest();
        $index = Elasticsearch::getInstance()->settings->getIndexByIndexName(
            Craft::$app->getRequest()->getBodyParam('index')
        );

        Queue::push(new BulkIndexJob([
            'index' => $index->name,
        ]));

        Craft::$app->getSession()->setSuccess(Craft::t('elasticsearch', 'Reindexing {index}.', [
            'index' => $index->name,
        ]));

        return $this->redirect(UrlHelper::cpUrl('utilities/elasticsearch'));
    }

    public function actionFlush(): Response
    {
        $this->requirePostRequest();
        $index = Elasticsearch::getInstance()->settings->getIndexByIndexName(
            Craft::$app->getRequest()->getBodyParam('index')
        );

        Queue::push(new FlushIndexJob([
            'index' => $index->name,
        ]));

        Craft::$app->getSession()->setSuccess(Craft::t('elasticsearch', 'Flushing {index}.', [
            'index' => $index->name,
        ]));

        return $this->redirect(UrlHelper::cpUrl('utilities/elasticsearch'));
    }

    public function actionUpdateMapping(): Response
    {
        $this->requirePostRequest();
        $index = Elasticsearch::getInstance()->settings->getIndexByIndexName(
            Craft::$app->getRequest()->getBodyParam('index')
        );

        Queue::push(new UpdateMappingJob([
            'index' => $index->name,
        ]));

        Craft::$app->getSession()->setSuccess(Craft::t('elasticsearch', 'Updating mappings for {index}.', [
            'index' => $index->name,
        ]));

        return $this->redirect(UrlHelper::cpUrl('utilities/elasticsearch'));
    }

    public function actionDelete(): Response
    {
        $this->requirePostRequest();
        $index = Elasticsearch::getInstance()->settings->getIndexByIndexName(
            Craft::$app->getRequest()->getBodyParam('index')
        );

        Elasticsearch::getInstance()->indexes->delete($index);

        Craft::$app->getSession()->setSuccess(Craft::t('elasticsearch', 'Deleting {index}.', [
            'index' => $index->name,
        ]));

        return $this->redirect(UrlHelper::cpUrl('utilities/elasticsearch'));
    }

    public function actionCreate(): Response
    {
        $this->requirePostRequest();
        $index = Elasticsearch::getInstance()->settings->getIndexByIndexName(
            Craft::$app->getRequest()->getBodyParam('index')
        );

        Elasticsearch::getInstance()->indexes->create($index);

        Craft::$app->getSession()->setSuccess(Craft::t('elasticsearch', 'Creating {index}.', [
            'index' => $index->name,
        ]));

        return $this->redirect(UrlHelper::cpUrl('utilities/elasticsearch'));
    }
}
