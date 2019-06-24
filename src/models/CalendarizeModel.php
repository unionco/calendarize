<?php
/**
 * Calendarize plugin for Craft CMS 3.x
 *
 * Calendar element types
 *
 * @link      https://union.co
 * @copyright Copyright (c) 2018 Franco Valdes
 */

namespace unionco\calendarize\models;

use Craft;
use craft\base\Model;
use craft\helpers\DateTimeHelper;
use craft\helpers\Json;
use DateTime;
use DateTimeZone;
use RRule\RRule;
use RRule\RSet;
use unionco\calendarize\Calendarize;

/**
 * @author    Franco Valdes
 * @package   Calendarize
 * @since     1.0.0
 */
class CalendarizeModel extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    private $owner;
    public $ownerId;
    public $ownerSiteId;
    public $fieldId;
    public $startDate;
    public $endDate;
    public $allDay = false;
    public $repeats = false;
    public $days = [];
    public $endRepeat = 'never';
    public $endRepeatDate;
    public $exceptions = [];
    public $timeChanges = [];
    public $repeatType = 'week';
    public $months = null;

    static protected $RRULEMAP = [
        'daily' => 'DAILY',
        'weekly' => 'WEEKLY',
        'biweekly' => 'WEEKLY',
        'monthly' => 'MONTHLY',
        'yearly' => 'YEARLY',
    ];

    static protected $RRULEDAYMAP = [
        0 => "SU",
        1 => "MO",
        2 => "TU",
        3 => "WE",
        4 => "TH",
        5 => "FR",
        6 => "SA",
    ];

    // Private Properties
    // =========================================================================

    /** @var CalendarModel[] */
    private $occurrenceCache;

    // Public Methods
    // =========================================================================
    public function __construct($owner, $attributes = [], array $config = [])
	{
		foreach ($attributes as $key => $value) {
			if (property_exists($this, $key)) {
				switch ($key) {
                    case 'startDate':
                    case 'endDate':
                    case 'endRepeatDate':
                        $this->{$key} = $this->_setDateType($value);
                        break;
                    case 'exceptions':
                        $value = is_string($value) ? Json::decode($value) : $value;
                        $this->{$key} = array_map(function ($e) {
                            return $this->_setDateType($e);
                        }, $value ?? []);
                        break;
                    case 'timeChanges':
                        $value = is_string($value) ? Json::decode($value) : $value;
                        $this->{$key} = array_map(function ($e) {
                            return $this->_setDateType($e);
                        }, $value ?? []);
                        break;
                    case 'days':
                        $this->{$key} = is_string($value) && isset($value) ? Json::decode($value) : $value;
                        break;
                    default:
                        $this->{$key} = $value;
                        break;
                }
            }
        }

        // Need to enforce an end date
        if (!isset($this->endDate) || empty($this->endDate) || !$this->endDate instanceof \DateTime) {
            $this->endDate = $this->startDate;
        }

        $this->owner = $owner;
		parent::__construct($config);
    }

    /**
	 * @inheritdoc
	 */
	public function isValueEmpty($value, ElementInterface $element): bool
	{
	    return (empty($value->startDate) && empty($value->endDate));
    }

    /**
     * Returns the calendar next occurrence
     *
     * @return string
     */
    public function __toString(): string
    {
        if ($next = $this->next()) {
            return $next->format('m/d/Y h:i a');
        }

        return '';
    }

    /**
     * Bool if end date exist
     *
     * @return bool
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Bool if end date exist
     *
     * @return bool
     */
    public function ends()
    {
        return $this->endRepeat !== "never";
    }

    /**
     * Gets the next occurrence datetime
     *
     * @return datetime
     */
    public function next()
    {
        if (empty($this->startDate) || empty($this->endDate)) {
            return false;
        }

        $today = DateTimeHelper::toDateTime(new DateTime('now', new DateTimeZone(Craft::$app->getTimeZone())));
        $numericValueOfToday = $today->format('w');
        $days = $this->days;
        $diff = $this->endDate->getTimestamp() - $this->startDate->getTimestamp();

        // This event isnt in range just yet...
        if ($today->format('Y-m-d') < $this->startDate->format('Y-m-d')) {
            return new Occurrence($this->owner, $this->startDate, $diff);
        }

        // if repeats find the next occurrence else return the start date
        if ($this->repeats) {
            if (!empty($this->endRepeatDate)) {
                $this->endRepeatDate->setTime($this->startDate->format('H'), $this->startDate->format('i'));
            }

            // if it ends at somepoint and we are passed that date, return the last occurrence
            if ($this->endRepeat !== 'never' && $today > $this->endRepeatDate) {
                return new Occurrence($this->owner, $this->endRepeatDate, $diff);
            }

            $occurrences = $this->getOccurrencesBetween($today, null, 1);

            if (count($occurrences)) {
                $nextOffer = $occurrences[0];

                if ($this->endRepeat !== 'never' && !empty($this->endRepeatDate)) {
                    if ($nextOffer > $this->endRepeatDate) {
                        return new Occurrence($this->owner, $this->endRepeatDate, $diff);
                    }
                }

                return $nextOffer;
            }
        }

        return new Occurrence($this->owner, $this->startDate, $diff);
    }

    /**
     * Get next occurrences
     *
     * @var int
     *
     * @return array
     */
    public function getOccurrences($limit = 10)
    {
        if (empty($this->startDate) || empty($this->endDate)) {
            return [];
        }

        $diff = $this->endDate->getTimestamp() - $this->startDate->getTimestamp();
        $occurrences = $this->rrule()->getOccurrences($limit);

        $this->_adjustTimeChanges($occurrences);

        return array_map(function($occurrence) use($diff) {
            return new Occurrence($this->owner, $occurrence, $diff);
        }, $occurrences);
    }

    /**
     * Get occurrences between two dates
     *
     * @param startDate string|Datetime
     * @param startDate string|Datetime
     *
     * @return array
     */
    public function getOccurrencesBetween($startDate, $endDate = null, $limit = 1)
    {
        if (empty($this->startDate) || empty($this->endDate)) {
            return [];
        }

        if (is_string($startDate)) {
            $startDate = DateTimeHelper::toDateTime(new DateTime($startDate, new DateTimeZone(Craft::$app->getTimeZone())));
        }

        if (isset($endDate) && is_string($endDate)) {
            $endDate = DateTimeHelper::toDateTime(new DateTime($endDate, new DateTimeZone(Craft::$app->getTimeZone())));
        }

        $diff = $this->endDate->getTimestamp() - $this->startDate->getTimestamp();
        $occurrences = $this->rrule()->getOccurrencesBetween($startDate, $endDate, $limit);

        $this->_adjustTimeChanges($occurrences);

        return array_map(function($occurrence) use($diff) {
            return new Occurrence($this->owner, $occurrence, $diff);
        }, $occurrences);
    }

    /**
     * Boolean if the element has passed
     *
     * @return boolean
     */
    public function hasPassed()
    {
        if (empty($this->startDate) || empty($this->endDate)) {
            return false;
        }

        $next = $this->next()->next;

        return DateTimeHelper::isInThePast($next);
    }

    /**
     * Gets the readable string from rrule
     *
     * @param opts array
     *
     * @return string
     */
    public function readable(array $opts = [])
    {
        if ($this->repeats) {
            return $this->rrule()->getRRules()[0]->humanReadable($opts);
        }
        return '';
    }

    /**
     * Initial rrule for field params
     *
     * @return rrule
     */
    public function rrule()
    {
        if (null === $this->occurrenceCache) {
            if ($this->repeats) {
                $config = [
                    'FREQ'       => strtoupper(static::$RRULEMAP[$this->repeatType]),
                    'INTERVAL'   => 1,
                    'DTSTART'    => $this->startDate,
                    'UNTIL'      => $this->endRepeat !== 'never' ? $this->endRepeatDate ?? $this->startDate : null
                ];

                if ($this->endRepeat === 'never') {
                    $today = DateTimeHelper::toDateTime(new DateTime('now', new DateTimeZone(Craft::$app->getTimeZone())));

                    if ($this->repeatType === 'yearly') {
                        $config['UNTIL'] = DateTimeHelper::toDateTime($today->modify('+5 years'));
                    } else {
                        $config['UNTIL'] = DateTimeHelper::toDateTime($today->modify('+1 year'));
                    }
                }
            } else {
                $config = [
                    'FREQ'       => "DAILY",
                    'INTERVAL'   => 1,
                    'DTSTART'    => $this->startDate,
                    'UNTIL'      => $this->startDate
                ];
                $this->repeatType = 'daily';
            }

            switch ($this->repeatType) {
                case 'daily':
                case 'yearly':
                    break;
                case 'weekly':
                    $config['BYDAY'] = array_map(function ($day) {
                        return static::$RRULEDAYMAP[$day];
                    }, array_keys($this->days ?? []));
                    break;
                case 'biweekly':
                    $config['BYDAY'] = array_map(function ($day) {
                        return static::$RRULEDAYMAP[$day];
                    }, array_keys($this->days ?? []));
                    $config['INTERVAL'] = 2;
                    break;
                case 'monthly':
                    if ($this->months === 'onMonthDay') {
                        $config['BYDAY'] = Calendarize::$plugin->calendar->weekOfMonth($this->startDate) . static::$RRULEDAYMAP[$this->startDate->format('w')];
                    }
                    break;
            }

            $rset = new RSet();
            $rset->addRRule($config);

            if ($this->exceptions) {
                foreach ($this->exceptions as $exception) {
                    $date = DateTimeHelper::toDateTime($exception);
                    $date->setTime($this->startDate->format('H'), $this->startDate->format('i'));
                    $rset->addExDate($date);
                }
            }

            // cache rset
            $this->occurrenceCache = $rset;
        }

        return $this->occurrenceCache;
    }

    /**
     * Return the ICS url for a specific event
     * @param array $options
     *
     * @return mixed
     */
    public function getIcsUrl($options = [])
    {
        return Calendarize::$plugin->ics->getUrl($this, $options);
    }

    /**
     * Return the ICS for all events in the parent section
     * @param array $options
     *
     * @return mixed
     */
    public function getCalendarIcsUrl($options = [])
    {
        return Calendarize::$plugin->ics->getCalendarIcsUrl($this, $options);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        parent::rules();
    }

    // Private Methods
    // =========================================================================

    /**
     *
     */
    private function _adjustTimeChanges(&$occurrences = [])
    {
        // set the start time to all occurrences
        $this->_adjustTimes($occurrences);

        // change out times if changes exists
        if (isset($this->timeChanges) && count($this->timeChanges)) {
            foreach ($occurrences as $key => $occurrence) {
                // find in occurrences
                $change = array_filter($this->timeChanges, function ($change) use ($occurrence) {
                    return $change->format('Y-m-d') === $occurrence->format('Y-m-d');
                });
                if ($change) {
                    $occurrences[$key] = array_shift($change);
                }
            }
        }
    }

    /**
     *
     */
    private function _adjustTimes(&$occurrences = [])
    {
        // change out times
        foreach ($occurrences as $key => $occurrence) {
            $occurrences[$key]->setTime($this->startDate->format('H'), $this->startDate->format('i'));
        }
    }

    /**
     *
     */
    private function _setDateType($value)
    {
        if (isset($value) && !empty($value)) {
            if ($value instanceof DateTime) {
                return $value;
            }

            if (is_array($value) && isset($value['timezone_type'])) {
                // revision
                $value = new DateTime($value['date'], new DateTimeZone($value['timezone']));
            }

            return DateTimeHelper::toDateTime($value);
        }
        return null;
    }
}
