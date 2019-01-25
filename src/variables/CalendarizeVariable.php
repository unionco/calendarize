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
use unionco\calendarize\Calendarize;

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
     * @return array
     */
    public function upcoming($criteria = null, $order = "asc")
    {
        return Calendarize::$plugin->calendar->upcoming($criteria, $order);
    }

    /**
     * Get entries after date
     * 
     * @param date date
     * @param criteria ElementCriteria
     * 
     * @return array
     */
    public function after($date = null, $criteria = null, $order = "asc")
    {
        return Calendarize::$plugin->calendar->after($date, $criteria);
    }
}
