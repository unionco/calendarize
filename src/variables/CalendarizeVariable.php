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
     * 
     */
    public function weekMonthText($date)
    {
        return Calendarize::$plugin->calendar->weekMonthText($date);
    }

    /**
     * 
     */
    public function upcoming($criteria = null)
    {
        return Calendarize::$plugin->calendar->upcoming($criteria);
    }

    /**
     * 
     */
    public function after($date = null, $criteria = null)
    {
        return Calendarize::$plugin->calendar->after($date, $criteria);
    }
}
