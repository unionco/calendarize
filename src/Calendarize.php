<?php
/**
 * Calendarize plugin for Craft CMS 3.x
 *
 * Calendar element types
 *
 * @link      https://union.co
 * @copyright Copyright (c) 2018 Franco Valdes
 */

namespace unionco\calendarize;

use Craft;
use craft\base\Element;
use craft\base\Plugin;
use craft\elements\Entry;
use craft\elements\db\ElementQuery;
use craft\elements\db\EntryQuery;
use craft\events\CancelableEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\RegisterElementSortOptionsEvent;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use craft\web\View;
use unionco\calendarize\fields\CalendarizeField;
use unionco\calendarize\models\Settings;
use unionco\calendarize\services\CalendarizeService;
use unionco\calendarize\services\ICS;
use unionco\calendarize\variables\CalendarizeVariable;
use yii\base\Event;

/**
 * Class Calendarize
 *
 * @author    Franco Valdes
 * @package   Calendarize
 * @since     1.0.0
 *
 * @property  CalendarizeServiceService $calendarizeService
 */
class Calendarize extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Calendarize
     */
    public static $plugin;

    // Public Properties
    // =========================================================================
    
    /**
     * @var boolean
     */
    public $hasSettings = false;

    /**
     * @var boolean
     */
    public $hasCpSection = false;

    /**
     * @var string
     */
    public $changelogUrl = "https://raw.githubusercontent.com/unionco/calendarize/master/CHANGELOG.md";

    /**
     * @var string
     */
    public $schemaVersion = '1.3.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->controllerNamespace = 'unionco\calendarize\controllers';

        $this->setComponents([
            'calendar' => CalendarizeService::class,
            'ics' => ICS::class
        ]);

        // Base template directory
        Event::on(
            View::class,
            View::EVENT_REGISTER_CP_TEMPLATE_ROOTS,
            function (RegisterTemplateRootsEvent $e) {
                if (is_dir($baseDir = $this->getBasePath().DIRECTORY_SEPARATOR.'templates')) {
                    $e->roots[$this->id] = $baseDir;
                }
            }
        );

        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = CalendarizeField::class;
            }
        );

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('calendarize', CalendarizeVariable::class);
            }
        );

        /**
         * Adding Sort Options
         */
        // Event::on(
        //     Entry::class, 
        //     Element::EVENT_REGISTER_SORT_OPTIONS, 
        //     function(RegisterElementSortOptionsEvent $event) {
        //         $event->sortOptions[] = [
        //             'label' => 'Calendarize | Start Date',
        //             'orderBy' => 'calendarize.startDate'
        //         ];
        //     }
        // );

        /**
         * Modifying query when sorting by calendarize
         */
        // Event::on(
        //     ElementQuery::class,
        //     ElementQuery::EVENT_BEFORE_PREPARE,
        //     function(CancelableEvent $event) {
        //         $query = $event->sender;
        //         if ($query instanceof EntryQuery) {
        //             if (isset($query->orderBy['calendarize.startDate'])) {
        //                 $direction = $query->orderBy['calendarize.startDate'];
        //                 unset($query->orderBy['calendarize.startDate']);
        //                 $query->join[] = ['JOIN', '{{%calendarize}} calendarize', '[[entries.id]] = [[calendarize.ownerId]]'];
        //                 $query->orderBy['calendarize.startDate'] = $direction;
        //             }
        //         }
        //     }
        // );

        Craft::info(
            Craft::t(
                'calendarize',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    public function afterInstall()
	{
		parent::afterInstall();
    }
    
    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'calendarize/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
