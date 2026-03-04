<?php

namespace GlueAgency\Elasticsearch\controllers;

use Craft;
use craft\web\Controller;
use GlueAgency\Elasticsearch\Elasticsearch;
use yii\web\Response;

class SettingsController extends Controller
{

    public function actionFieldsTemplateForAuthType(): Response
    {
        $request = Craft::$app->getRequest();

        if(! $type = $request->get('type')) {
            return $this->asJson([
                'error' => Craft::t('elasticsearch', 'Missing type'),
            ]);
        }

        $view = Craft::$app->getView();
        $html = $view->renderTemplate("elasticsearch/settings/auth/{$type}", [
            'settings' => Elasticsearch::getInstance()->getSettings(),
        ]);

        return $this->asJson([
            'settingsHtml' => $html,
            'headHtml' => $view->getHeadHtml(),
            'bodyHtml' => $view->getBodyHtml(),
        ]);
    }
}
