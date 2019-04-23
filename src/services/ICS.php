<?php
/**
 * Calendarize plugin for Craft CMS 3.x
 *
 * Calendar element types
 *
 * @link      https://union.co
 * @copyright Copyright (c) 2018 Franco Valdes
 */

namespace unionco\calendarize\services;

use Craft;
use craft\base\Component;
use craft\helpers\FileHelper;
use DateTime;
use DateTimeZone;
use unionco\calendarize\Calendarize;
use unionco\calendarize\models\CalendarizeModel;

/**
 * @author    Franco Valdes
 * @package   Calendarize
 * @since     1.0.0
 */
class ICS extends Component
{

    /**
     * Return the ICS url for a specific event
     * @param array $options
     *
     * @return mixed
     */
    public function getUrl(CalendarizeModel $model, $options)
    {
        $params = http_build_query([
            'filename' => $filename = $options['filename'] ?? null,
            'ownerId' => $model->ownerId,
            'ownerSiteId' => $model->ownerSiteId,
            'fieldId' => $model->fieldId
        ]);
        return "/actions/calendarize/default/make-ics?" . $params;
    }

    /**
     * Return the ICS for all events in the parent section
     * @param array $options
     *
     * @return mixed
     */
    public function getCalendarIcsUrl(CalendarizeModel $model, $options)
    {
        $params = http_build_query([
            'sectionId' => $model->getOwner()->getSection()->id,
            'siteId' => $model->ownerSiteId,
            'fieldId' => $model->fieldId,
            'relatedTo' => $options['relatedTo'] ?? null,
            'filename' => $options['filename'] ?? null
        ]);

        return "/actions/calendarize/default/make-section-ics?" . $params;
    }

    /**
	 *
	 */
	public function make(CalendarizeModel $model, $filename = null)
	{
        $owner = $model->getOwner();
        $rule = $model->rrule()->getRRules()[0];
        $filename = $filename ? $filename : $owner->slug;

		$cal = "BEGIN:VCALENDAR\n".
                "VERSION:2.0\n".
                "PRODID:-//CALENDARIZE Craft //EN\n".
                $this->_makeEvent($model);
                "END:VCALENDAR\n";

        $storage = Craft::$app->getPath()->getStoragePath();
        $path = $storage . "/calendarize/" . $filename . ".ics";
        $file = FileHelper::writeToFile($path, $cal);

        return $path;
    }

    /**
     *
     */
    public function makeEvents($events, $filename = null)
    {

        $cal = "BEGIN:VCALENDAR\n".
            "VERSION:2.0\n".
            "PRODID:-//CALENDARIZE Craft //EN\n";
        $filename = $filename ? $filename : $event[0]->getOwner()->getsection()->slug;

        foreach($events as $events) {
            $cal .= $this->_makeEvent($events);
        }

        $cal .= "END:VCALENDAR\n";

        $storage = Craft::$app->getPath()->getStoragePath();
        $path = $storage . "/calendarize/" . $filename . ".ics";
        $file = FileHelper::writeToFile($path, $cal);

        return $path;
    }

    private function _makeEvent(CalendarizeModel $model)
    {
        $owner = $model->getOwner();
        $rule = $model->rrule()->getRRules()[0];

        $ics = "BEGIN:VEVENT\n".
        $rule->rfcString()."\n".
        "DTEND;TZID=". $model->endDate->getTimezone()->getName().":". $model->endDate->format('Ymd\THis') ."\n".
        "SUMMARY:".$this->_escapeString($owner->title)."\n".
        "DESCRIPTION:\n".
        "URL;VALUE=URI:".$owner->url."\n".
        "UID:".uniqid()."\n".
        "DTSTAMP:".$this->_dateToCal()."\n".
        "END:VEVENT\n";

        return $ics;
    }

    /**
	 * Generate the specific date markup for a ics file
	 *
	 * @param  integer $timestamp Timestamp to be transformed
	 * @return string
	 */
	private function _dateToCal(DateTime $dateTime = null)
	{
        if (!$dateTime) {
            $dateTime = new DateTime('now');
        }

        return $dateTime->setTimezone(new \DateTimeZone("UTC"))->format('Ymd\THis\Z');
	}
	/**
	 * Escape characters
	 *
	 * @param  string $string String to be escaped
	 * @return string
	 */
	private function _escapeString($string)
	{
		return preg_replace('/([\,;])/','\\\$1', ($string) ? $string : '');
	}
}
