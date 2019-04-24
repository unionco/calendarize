<?php
/**
 * Calendarize plugin for Craft CMS 3.x
 *
 * Calendar element types
 *
 * @link      https://union.co
 * @copyright Copyright (c) 2018 Franco Valdes
 */
namespace unionco\calendarize\fields;

use yii\validators\Validator;

class CalendarizeValidator extends Validator {

	/**
	 * @inheritdoc
	 */
	protected function validateValue($value)
	{
        if ($value->startDate && !$value->endDate) {
            return [
                \Craft::t(
                    'calendarize',
                    'End Date must be set if start date is not null'
                ),
                []
            ];
        }

        if ($value->startDate && $value->endDate) {
            if ($value->endDate < $value->startDate) {
                return [
                    \Craft::t(
                        'calendarize',
                        'End Date must be greater than or equal to your Start Date'
                    ),
                    []
                ];
            }
        }

        if (isset($value->endRepeat) && $value->endRepeat === 'date' && !$value->endRepeatDate) {
            return [
                    \Craft::t(
                        'calendarize',
                        'End Repeat Date is required if repeating ends on date'
                    ),
                    []
                ];
        }
        
		return null;
	}

}