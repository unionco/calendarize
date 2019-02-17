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

use unionco\calendarize\Calendarize;

use Craft;
use craft\web\Controller;
use unionco\calendarize\records\CalendarizeRecord;
use unionco\calendarize\models\CalendarizeModel;
use craft\base\Element;

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
    protected $allowAnonymous = ['make-ics'];

    // Public Methods
    // =========================================================================

    /**
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
}
