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
use DateTime;
use ReflectionClass;
use craft\base\Element;

class Occurrence
{
    // Public Properties
    // =========================================================================

    /**
     * @var Element
     */
    public $element;

    /**
     * @var string
     */
    public $next;

    /**
     * @var string
     */
    public $start;

    /**
     * @var string
     */
    public $end;

    /**
     *
     */
    public function __construct(Element $element, DateTime $next, int $diff)
    {
        $this->element = $element;
        $this->next = $next;

        // start and end date
        $this->start = $next;

        // end date
        $end = clone $next;
        $this->end = $end->modify($diff . ' seconds');
    }

    /**
     * Fall back to element attributes
     */
    public function __call($name, $args = [])
    {
        // backwards compatibility
        if (in_array($name, get_class_methods(DateTime::class))) {
            return $this->next->{$name}(...$args);
        }

        if (!isset($this->{$name})) {
            return $this->element->{$name};
        }

        return $this->{$name};
    }

    /**
     *
     */
    public function __toString()
    {
        return $this->next->format('U');
    }

    /**
     *
     */
    public function getType(): string
    {
        return (new ReflectionClass($this->element))->getShortName();
    }
}
