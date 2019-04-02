<?php
/**
 * Calendarize plugin for Craft CMS 3.x
 *
 * Calendar element types
 *
 * @link      https://union.co
 * @copyright Copyright (c) 2018 Franco Valdes
 */

namespace unionco\calendarize\variables;

use Craft;
use craft\elements\Entry;
use craft\elements\MatrixBlock;
use unionco\calendarize\Calendarize;
use unionco\calendarize\models\CalendarizeModel;

/**
 * @author    Franco Valdes
 * @package   Calendarize
 * @since     1.0.0
 */
class CalendarizeVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Get week month text
     *
     * @param date date
     *
     * @return string
     */
    public function weekMonthText($date)
    {
        return Calendarize::$plugin->calendar->weekMonthText($date);
    }

    /**
     * Get upcoming entries
     *
     * @param criteria ElementCriteria
     * @param order string
     *
     * @return array Occurance[]
     */
    public function upcoming($criteria = [], $order = "asc", $unique = false)
    {
        return Calendarize::$plugin->calendar->upcoming($criteria, $order, $unique);
    }

    /**
     * Get entries after date
     *
     * @param date date
     * @param criteria ElementCriteria
     * @param order string
     *
     * @return array Occurance[]
     */
    public function after($date = null, $criteria = [], $order = "asc", $unique = false)
    {
        return Calendarize::$plugin->calendar->after($date, $criteria, $order, $unique);
    }

    /**
     * Get entries between two dates
     *
     * @param start string|date
     * @param end string|date
     * @param criteria ElementCriteria
     * @param order string
     *
     * @return array Occurance[]
     */
    public function between($start, $end, $criteria = [], $order = "asc", $unique = false)
    {
        return Calendarize::$plugin->calendar->between($start, $end, $criteria, $order, $unique);
    }
}
