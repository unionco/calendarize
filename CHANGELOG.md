# Calendarize Changelog

## 1.2.2 - 2019-02-11

### Fixed
- Fixed RRULE usage when occurrences are NOT repeating.

## 1.2.1 - 2019-02-11

### Updated
- Made sure all instance of the work occurrence were consistent.

## 1.2.0 - 2019-02-11

### Added
- New variable `between` to allow query to find occurrences between two dates. Similar to the `after` method but restricting an end date as well.
- New `Occurence` model to more easily sort occurrences and access the `next` property. On the twig side, this new change is backwards compatible so no changes needed. 

### Updated
- Updated javascript to allow for multiple calendarize fields on the page and also in efforts to support matrix usage soon.


## 1.1.16 - 2019-01-31

## Fixed
- Sorting method wasn't checking the field type correctly and calling `next` on none calendarize field types.

## 1.1.15 - 2019-01-30

## Added
- RRULE humanreadable options [RRULE DOCS](https://github.com/rlanvin/php-rrule/wiki/RRule#humanreadablearray-opt)
- From RRULE Package: "Note: this option method will produce better results if PHP intl extension is available."

## Fixed
- Upcoming entries still showing non repeating past events
- RRULE usage when entry does not repeat is no longer possible 

## 1.1.13 - 2019-01-24

### Added

- Order parameter to `craft.calendarize.upcoming` method
- Order parameter to `craft.calendarize.after` method

### Fixed

- Upcoming entries query 


## 1.1.9 - 2019-01-19

### Added

- Yearly repeat option
- Biweekly repeat option

## 1.1.8 - 2019-01-14

### Update

- Update weekMonthText and weekOfMonth


## 1.1.7 - 2019-01-14

### Update

- Updating database schema, fixing __toString method.


## 1.1.6 - 2019-01-03

### Update

- Update composer, changelog url, documentation url


## 1.1.5 - 2019-01-03

### Update

- Fix changelog url


## 1.1.4 - 2019-01-03

### Update

- Added `__toString` method to calendarize model. Returns the next occurence.
- Added caching to upcoming and after queries as well as rrule occurence creator


## 1.1.3 - 2019-01-02

### Updated

- Updating and refactored javascript


## 1.0.0 - 2018-12-19

### Added

- Initial release
