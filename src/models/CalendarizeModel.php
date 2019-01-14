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
        'monthly' => 'MONTHLY'
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
    private $occurenceCache;

    // Public Methods
    // =========================================================================
    public function __construct($attributes = [], array $config = [])
	{
		foreach ($attributes as $key => $value) {
			if (property_exists($this, $key)) {
				switch ($key) {
                    case 'startDate':
                    case 'endDate':
                    case 'endRepeatDate':
                        $this->{$key} = DateTimeHelper::toDateTime($value);
                        break;
                    case 'exceptions':
                        $value = is_string($value) ? Json::decode($value) : $value;
                        $this->{$key} = array_map(function ($e) {
                            return DateTimeHelper::toDateTime($e);
                        }, $value ?? []);
                        break;
                    case 'timeChanges':
                        $value = is_string($value) ? Json::decode($value) : $value;
                        $this->{$key} = array_map(function ($e) {
                            return DateTimeHelper::toDateTime($e);
                        }, $value ?? []);
                        break;
                    case 'days':
                        $this->{$key} = is_string($value) ? Json::decode($value) : $value;
                        break;
                    default:
                        $this->{$key} = $value;
                        break;
                }
            }
        }

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
     * Returns the calendar next occurence
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
     * 
     */
    public function ends()
    {
        return $this->endRepeat !== "never";
    }

    /**
     * 
     */
    public function next()
    {
        if (empty($this->startDate) && empty($this->endDate)) {
            return false;
        }
        
        $today = DateTimeHelper::toDateTime(new DateTime('now', new DateTimeZone(Craft::$app->getTimeZone())));
        $numericValueOfToday = $today->format('w');
        $days = $this->days;
        
        // This event isnt in range just yet...
        if ($today->format('Y-m-d') < $this->startDate->format('Y-m-d')) {
            return $this->startDate;
        }

        // if repeats find the next occurence else return the start date
        if ($this->repeats) {
            if (isset($this->endRepeatDate)) {
                $this->endRepeatDate->setTime($this->startDate->format('H'), $this->startDate->format('i'));
            }

            // if it ends at somepoint and we are passed that date, return the last occurence
            if ($this->endRepeat !== 'never' && $today > $this->endRepeatDate) {
                return $this->endRepeatDate;
            }

            $occurences = $this->rrule()->getOccurrencesBetween($today, null, 1);

            $this->_adjustTimeChanges($occurences);

            if (count($occurences)) {
                $nextOffer = $occurences[0];

                if ($this->endRepeat !== 'never' && $this->endRepeatDate) {
                    if ($nextOffer > $this->endRepeatDate) {
                        return $this->endRepeatDate;
                    }
                }

                return $nextOffer;
            }
            
        }

        return $this->startDate;
    }

    /**
     * 
     */
    public function getOccurences($limit = 10)
    {
        if (empty($this->startDate) && empty($this->endDate)) {
            return [];
        }

        $occurences = $this->rrule()->getOccurrences($limit);
        
        $this->_adjustTimeChanges($occurences);
        
        return $occurences;
    }

    /**
     * 
     */
    public function getOccurrencesBetween($startDate, $endDate = null, $limit = 1)
    {
        if (empty($this->startDate) && empty($this->endDate)) {
            return [];
        }

        if (is_string($startDate)) {
            $startDate = DateTimeHelper::toDateTime(new DateTime($startDate, new DateTimeZone(Craft::$app->getTimeZone())));
        }

        if (isset($endDate) && is_string($endDate)) {
            $endDate = DateTimeHelper::toDateTime(new DateTime($endDate, new DateTimeZone(Craft::$app->getTimeZone())));
        }

        $occurences = $this->rrule()->getOccurrencesBetween($startDate, $endDate, $limit);

        $this->_adjustTimeChanges($occurences);

        return $occurences;
    }

    /**
     * 
     */
    public function hasPassed()
    {
        if (empty($this->startDate) && empty($this->endDate)) {
            return false;
        }

        $next = $this->next();

        return DateTimeHelper::isInThePast($next);
    }

    /**
     * 
     */
    public function readable()
    {
        return $this->rrule()->getRRules()[0]->humanReadable();
    }

    /**
     * 
     */
    public function rrule()
    {
        if (null === $this->occurenceCache) {
            $config = [
                'FREQ'       => strtoupper(static::$RRULEMAP[$this->repeatType]),
                'INTERVAL'   => 1,
                'DTSTART'    => $this->startDate,
                'UNTIL'      => $this->endRepeat !== 'never' ? $this->endRepeatDate ?? $this->startDate : null
            ];
            
            if ($this->endRepeat === 'never') {
                $today = DateTimeHelper::toDateTime(new DateTime('now', new DateTimeZone(Craft::$app->getTimeZone())));
                $config['UNTIL'] = DateTimeHelper::toDateTime($today->modify('+2 months'));
            }

            switch ($this->repeatType) {
                case 'daily':
                    break;
                case 'weekly':
                    $config['BYDAY'] = array_map(function ($day) {
                        return static::$RRULEDAYMAP[$day];
                    }, array_keys($this->days ?? []));
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
            $this->occurenceCache = $rset;
        }
        
        return $this->occurenceCache;
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
    private function _adjustTimeChanges(&$occurences = [])
    {
        // set the start time to all occurences
        $this->_adjustTimes($occurences);

        // change out times if changes exists
        if (isset($this->timeChanges) && count($this->timeChanges)) {
            foreach ($occurences as $key => $occurence) {
                // find in occurences
                $change = array_filter($this->timeChanges, function ($change) use ($occurence) {
                    return $change->format('Y-m-d') === $occurence->format('Y-m-d');
                });
                if ($change) {
                    $occurences[$key] = array_shift($change);
                }
            }
        }
    }

    /**
     * 
     */
    private function _adjustTimes(&$occurences = [])
    {
        // change out times
        foreach ($occurences as $key => $occurence) {
            $occurences[$key]->setTime($this->startDate->format('H'), $this->startDate->format('i'));
        }
    }
}
