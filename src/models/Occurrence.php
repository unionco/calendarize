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

use craft\base\Element;
use DateTime;
use ReflectionClass;

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
     * 
     */
    public function __construct(Element $element, DateTime $next)
    {
        $this->element = $element;
        $this->next = $next;
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
    public function getType(): string
    {
        return (new ReflectionClass($this->element))->getShortName();
    }
}