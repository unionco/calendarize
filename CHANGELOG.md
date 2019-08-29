# Calendarize Changelog

All notable changes to this project will be documented in this file.

## 1.2.16 - 2019-08-29
### Updated
- Updating wording around the repeat frequency closes [#28](https://github.com/unionco/calendarize/issues/28)
- Updating minute increment to 15 instead of 30. Will move this into a field setting in next update [#30](https://github.com/unionco/calendarize/issues/30)

## 1.2.15 - 2019-06-24
### Updated
- Updating deps due to vulnerability report from github

## 1.2.14 - 2019-06-24
### Updated
- Updated Docs
- Updated logo and icon
- Merged PR [#33](https://github.com/unionco/calendarize/pull/33)
- Merged PR [#31]https://github.com/unionco/calendarize/pull/31)

## 1.2.13 - 2019-06-24
### Fixed
- Enforcing the end date value. Fixes an error when no end date is provided. Will now default to the start date.

## 1.2.12 - 2019-05-02
### Added
Ability to access end date of next occurrence [#6](https://github.com/unionco/calendarize/issues/6)
Updated README to show new changes.

### Updated
Removed unused use statements

## 1.2.11 - 2019-04-23

### Fixed
Fixed field validation issue not allowing users to add a new calendarize field

## 1.2.10 - 2019-04-23
### Added
New custom validator to prevent calendarize from saving without required sub fields.

### Fixed
Fixed the week month method to get the accurate week of the month for repeat rules

## 1.2.9 - 2019-04-22
### Added
Allowing `between()` to pull entries from past dates. Thanks to (incraigulous)[https://github.com/incraigulous]
Cleaned up use blocks to remove used classes

## 1.2.8 - 2019-03-21
### Added
- New "unique" parameter added to all calendarize methods (after|between|upcoming) that will limit occurrences to 1 per entry instead of listing all the occurrences. Read me will reflect new signature. (#17)[https://github.com/unionco/calendarize/issues/17]

### Fixed
- Fixed a query issue when using the search criteria param (#21)[https://github.com/unionco/calendarize/issues/21]

## 1.2.7 - 2019-03-09
### Fixed
- Fixing up the docs (#18)[https://github.com/unionco/calendarize/issues/18]

## 1.2.6 - 2019-02-17
### Added
- Craft translate methods to cp templates to allow for locale translations (#4)[https://github.com/unionco/calendarize/issues/4]
- ICS url helper method to get the controller action to create a downloadable calendar file
- ICS controller action downloads a .ics file

### Fixed
- Fixed issue where reverting to an old entry would lose field data (#11)[https://github.com/unionco/calendarize/issues/11]

## 1.2.5 - 2019-02-14
### Fixed
- Fixing conditionals in template that were causing date selects to not save. Fixes [#15](https://github.com/unionco/calendarize/issues/15)

## 1.2.4 - 2019-02-13
### Added
- For backwards compatibility a __toString method was added to the Occurrence model.

## 1.2.3 - 2019-02-12
### Fixed
- Fixed future occurrences from showing up in `between` date method.

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
