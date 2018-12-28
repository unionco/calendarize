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
    public $repeatType = 'week';
    public $months = null;

    static $RRULEMAP = [
        'daily' => 'DAILY',
        'weekly' => 'WEEKLY',
        'monthly' => 'MONTHLY'
    ];

    static $RRULEDAYMAP = [
        0 => "SU",
        1 => "MO",
        2 => "TU",
        3 => "WE",
        4 => "TH",
        5 => "FR",
        6 => "SA",
    ];

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
                        }, $value);
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
            if (count($occurences)) {
                $nextOffer = $occurences[0];

                if ($this->endRepeat !== 'never' && $this->endRepeatDate) {
                    if ($nextOffer > $this->endRepeatDate) {
                        return $this->endRepeatDate;
                    }
                }

                $nextOffer->setTime($this->startDate->format('H'), $this->startDate->format('i'));
                return $nextOffer;
            }
            
        }

        return $this->startDate;
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

        return $rset;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        parent::rules();
    }
}
