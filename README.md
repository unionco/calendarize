![Screenshot](resources/img/calendarize.png)

# Calendarize Field Type plugin for Craft CMS 3.x
This plugin adds a calendarize field type that provides an interface to have repeating dates just like on a calendar interface. Repeat daily, weekly, and monthly with multiple other configurations per repeat type. Also comes with the ability add exception dates.

---
## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later and PHP7+.

---
## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require unionco/calendarize

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Calendarize.

---
## Calendarize Overview

Configuration is as follows:
1. Start Date - Datetime field
2. End Date - Datetime field
3. All Day - Lightswitch
4. Repeats - Lightswitch
5. Repeat Type - Dropdown [Daily, Weekly, BiWeekly, Monthly, Yearly]
6. Week Selector - Checkboxes
7. Monthly Selector - Dropdown [On the date, On the weekday of month]
8. Repeat Ends - Dropdown [Never, On Date]
9. Repeat End Date - Datetime field
10. Exceptions - Date fields Repeater (custom)
10. Time Changes - Datetime fields Repeater (custom)

![Screenshot](resources/img/field-layout.png)

---
## Usage

There are two ways to use the calendarize field in your templates.

### Normal craft queries

- When querying entries from craft you have the ability to access the calendarize field as you would with any other field.

        {% set events = craft.entries({ section: 'events' }).all %}
        {% for event in events %}
            {{ event.calendarizeHandle|date('Y-m-d' }} // __toString returns the next occurrence
            {{ event.calendarizeHandle.next|date('Y-m-d') }} // same as above
            {{ event.calendarizeHandle.startDate|date('Y-m-d') }} // original start date
            {{ event.calendarizeHandle.endDate|date('Y-m-d') }} // original end date
            {{ event.calendarizeHandle.ends }} // boolean if repeat ends
            {{ event.calendarizeHandle.repeats }} // boolean if entry repeats
            {{ event.calendarizeHandle.allDay }} // boolean all day entry
            {{ event.calendarizeHandle.repeatType }} // string type of repeat
            {{ event.calendarizeHandle.hasPassed }} // boolean if entry next occurrence has passed
            {{ event.calendarizeHandle.readable }} // string see rrule for more information
            {{ event.calendarizeHandle.getIcsUrl }} // url to ics controller action
        {% endfor %}

- There is also a few added methods to help get all occurrences for repeating entries. These helpers will return an array of `Occurrence` models.

        {% set occurrences = event.calendarizeHandle.getOccurrences(limit) %} {# limit is optional and defaults to 10 #}
        {% set occurrences = event.calendarizeHandle.getOccurrencesBetween(start, end, limit) %} {# limit is optional and defaults to 1 #}
        {% for occurrence in occurrences %}
            {{ occurrence.next|date('Y-m-d') }}
        {% endfor %}

### The calendarize query way

- Using the calendarize query variable will return all occurrences for all entries that match your criteria. This query returns an array of `Occurrence` models. You still have access to the parent element as you would with the craft query way. 

        {% set monthStart = '2022-01-01' %}
        {% set monthEnd = '2022-01-31' %}
        {% set occurrences = craft.calendarize.between(monthStart, monthEnd, { section: ['liveShows'] }) %}
        {% for occurrence in occurrences %}
            {{ occurrence.title }} @ {{ occurrence.next | date('Y-m-d') }}
        {% endfor %}

### Other Examples:
- This queries entries with calendarize fields with upcoming occurrences. Can take any normal criteria, order (asc, desc) and a unique parameter. The unique param will limit occurrences to 1 per entry instead of listing all the occurrences. Returns array of entries.

        {% set order = 'asc' %} // defaults to asc
        {% set unique = true %} // defaults to false 
        {% set entries = craft.calendarize.upcoming({ section: ['events'] }, order, unique) %}

- This queries entries with calendarize fields with occurrences after the provided date. Can take any normal criteria, order and a unique parameter. The unique param will limit occurrences to 1 per entry instead of listing all the occurrences.. Returns array of entries.
    
        {% set order = 'asc' %} // defaults to asc
        {% set unique = true %} // defaults to false 
        {% set entries = craft.calendarize.after('2019-01-04', { section: ['events'] }, order, unique) %}

- This queries entries with calendarize fields with occurrences between the provided dates. Can take any normal criteria, order and a unique parameter. The unique param will limit occurrences to 1 per entry instead of listing all the occurrences.. Returns array of entries.
    
        {% set order = 'asc' %} // defaults to asc
        {% set unique = true %} // defaults to false 
        {% set entries = craft.calendarize.between('2019-01-01', '2019-01-31', { section: ['events'] }, order, unique) %}


### Getting ICS URLs

- For a single event: 
 
        <a href="{{ event.getIcsUrl }}">Add to Calendar</a>
        
- For all events in a calendar: 
 
        <a href="{{ event.getCalendarIcsUrl }}">Subscribe</a>
---
## Models
### Calendarize Model

- Private Properties
    - owner
- Public Properties
    - ownerId
    - ownerSiteId
    - fieldId
    - startDate
    - endDate
    - allDay
    - repeats
    - days
    - endRepeat
    - endRepeatDate
    - exceptions
    - timeChanges
    - repeatType
    - months
- Public Methods
    - `getOwner(): Element`
    - `ends(): bool`
    - `next(): DateTime`
    - `getOccurrences($limit = 10): Occurrence[]`
    - `getOccurrencesBetween($startDate, $endDate = null, $limit = 1): Occurrence[]`
    - `hasPassed(): bool`
    - `readable(array $opts = []): string`
    - `rrule(): RSet`
    - `getIcsUrl(): string`

### Occurrence Model

- Public Properties
    - element
    - next
- Public Methods
    - `getType(): string`

---
## Dependencies 

- RRULE
    - This plugin leverages the use of the PHP RRule library. Docs for this can be found here [PHP RRule](https://github.com/rlanvin/php-rrule). The `rrule` method returns the pre configured rrule with all its available methods. In addition, the `getOccurrences` method returns all occurrences of the entry with a `limit` of 10 by default and the `getOccurrencesBetween` returns the occurence between 2 dates. If the end date is null, it will not enforce the end date and give all occurence greater than the start date provided.

---
## Calendarize Roadmap

### Matrix Support
Although the calendarize field _can_ be used in a Matrix Block context, the occurence queries (`upcoming`, `after`, `between`) will not find those elements. Currently, we are just using the `EntryQuery` to populate occurrences. We are working on a way to allow for querying of Matrix data as well.


Brought to you by [Franco Valdes](https://union.co)
