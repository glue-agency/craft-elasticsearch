<?php

namespace GlueAgency\Elasticsearch;

use Craft;
use craft\base\Element;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\ModelEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\helpers\Json;
use craft\services\Utilities;
use craft\web\twig\variables\CraftVariable;
use craft\web\View;
use GlueAgency\Elasticsearch\assetbundles\ElasticsearchAsset;
use GlueAgency\Elasticsearch\assetbundles\SettingsAsset;
use GlueAgency\Elasticsearch\models\Settings;
use GlueAgency\Elasticsearch\services\ClientService;
use GlueAgency\Elasticsearch\services\DocumentService;
use GlueAgency\Elasticsearch\services\ElementService;
use GlueAgency\Elasticsearch\services\IndexService;
use GlueAgency\Elasticsearch\services\IndexingService;
use GlueAgency\Elasticsearch\services\MappingService;
use GlueAgency\Elasticsearch\utilities\ElasticsearchUtility;
use GlueAgency\Elasticsearch\variables\ElasticsearchVariable;
use yii\base\Event;

/**
 * Elasticsearch plugin
 *
 * @method static Elasticsearch getInstance()
 *
 * @method Settings getSettings()
 * @property Settings        $settings
 *
 * @property ClientService   $client
 * @property DocumentService $documents
 * @property ElementService  $elements
 * @property IndexingService $indexing
 * @property IndexService    $indexes
 * @property MappingService  $mapping
 *
 * @author Glue Agency
 * @copyright Glue Agency
 * @license MIT
 */
class Elasticsearch extends Plugin
{
    public string $schemaVersion = '1.0.0';

    public bool $hasCpSettings = true;

    public static function config(): array
    {
        return [
            'components' => [
                'client'    => ClientService::class,
                'documents' => DocumentService::class,
                'elements'  => ElementService::class,
                'indexes'   => IndexService::class,
                'indexing'  => IndexingService::class,
                'mapping'   => MappingService::class,
            ],
        ];
    }

    public function init(): void
    {
        Craft::setAlias('@elasticsearch', $this->getBasePath());

        parent::init();

        $this->registerCraftVariable();
        $this->registerCpAsset();
        $this->attachEventHandlers();

        // Defer most setup tasks until Craft is fully initialized
        Craft::$app->onInit(function() {
            $this->registerControllers();
        });
    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    protected function settingsHtml(): ?string
    {
        $view = Craft::$app->getView();
        $view->registerAssetBundle(SettingsAsset::class);
        $view->registerJs('new Craft.Elasticsearch.Settings('.
            Json::encode('settings') .
        ');');

        return $view->renderTemplate('elasticsearch/settings/index', [
            'settings'   => $this->getSettings(),
            'sites'      => Craft::$app->getSites()->getAllSites(),
            'entryTypes' => Craft::$app->getEntries()->getAllEntryTypes(),
        ]);
    }

    protected function registerControllers(): void
    {
        if(Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'GlueAgency\\Elasticsearch\\console\\controllers';

            return;
        }

        $this->controllerNamespace = 'GlueAgency\\Elasticsearch\\controllers';
    }

    protected function registerCraftVariable(): void
    {
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function (Event $event) {
            $event->sender->set('elasticsearch', ElasticsearchVariable::class);
        });
    }

    protected function registerCpAsset(): void
    {
        if(Craft::$app->getRequest()->getIsCpRequest()) {
            Craft::$app->getView()->registerAssetBundle(ElasticsearchAsset::class);
        }
    }

    protected function attachEventHandlers(): void
    {
        Event::on(View::class, View::EVENT_REGISTER_CP_TEMPLATE_ROOTS, function(RegisterTemplateRootsEvent $event) {
            $event->roots['elasticsearch'] = $this->getBasePath() . DIRECTORY_SEPARATOR . 'templates';
        });

        Event::on(Utilities::class, Utilities::EVENT_REGISTER_UTILITIES, function (RegisterComponentTypesEvent $event) {
            $event->types[] = ElasticsearchUtility::class;
        });

        Event::on(Element::class, Element::EVENT_AFTER_SAVE, function(ModelEvent $event) {
            Elasticsearch::getInstance()->indexing->index($event->sender);
        });

        Event::on(Element::class, Element::EVENT_AFTER_DELETE, function(Event $event) {
            Elasticsearch::getInstance()->indexing->delete($event->sender);
        });
    }
}
