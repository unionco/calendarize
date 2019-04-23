<?php
/**
 * Calendarize plugin for Craft CMS 3.x
 *
 * Calendar element types
 *
 * @link      https://union.co
 * @copyright Copyright (c) 2018 Franco Valdes
 */

namespace unionco\calendarize\controllers;

use Craft;
use craft\base\Field;
use craft\elements\Entry;
use craft\records\Field as FieldRecord;
use craft\records\Section;
use craft\web\Controller;
use unionco\calendarize\Calendarize;
use unionco\calendarize\models\CalendarizeModel;
use unionco\calendarize\records\CalendarizeRecord;
use unionco\calendarize\services\ICS;

/**
 * @author    Franco Valdes
 * @package   Calendarize
 * @since     1.0.0
 */
class DefaultController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['make-ics', 'make-section-ics'];

    // Public Methods
    // =========================================================================

    /**
     * Download an ICS file for a single event.
     * @return mixed
     */
    public function actionMakeIcs(int $ownerId, int $ownerSiteId, int $fieldId)
    {
        $record = CalendarizeRecord::findOne(
			[
				'ownerId'     => $ownerId,
				'ownerSiteId' => $ownerSiteId,
				'fieldId'     => $fieldId,
			]
        );
        $owner = $record->getOwner()->one();
        $element = $owner->type::find()
            ->id($owner->id)
            ->one();

        $model = new CalendarizeModel($element, $record->getAttributes());
        $ics = Calendarize::$plugin->ics->make($model);

        $response = Craft::$app->getResponse();

        return $response->sendFile($ics, null, ['inline' => true]);
    }

    /**
     * Download an ICS file for all events in a section.
     * @return mixed
     */
    public function actionMakeSectionIcs(int $sectionId, int $siteId, int $fieldId, $relatedTo = null, $filename = null)
    {
        $field = FieldRecord::findOne($fieldId);
        $fieldHandle = $field->handle;
        $section = Section::findOne($sectionId);

        $entries = Entry::find()
            ->sectionId($sectionId)
            ->siteId($siteId)
            ->relatedTo($relatedTo)
            ->all();

        $events = array_reduce($entries, function($carry, $entry) use ($fieldHandle) {
               if ($event = $entry->$fieldHandle) {
                   if ($event->startDate && $event->endDate) {
                       $carry[] = $event;
                   }
               }
               return $carry;
        }, []);

        $ics = Calendarize::$plugin->ics->makeEvents($events, $filename);
        $response = Craft::$app->getResponse();

        return $response->sendFile($ics, null, ['inline' => true]);
    }
}
