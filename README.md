# VWM Surveys

VWM Surveys is a survey module for ExpressionEngine 2. It requires **PHP 5.3** or greater.

## License

VWM Surveys is licensed under the [Apache 2 License](http://www.apache.org/licenses/LICENSE-2.0.html).

## Change Log

### 0.5.2

* Fix [installation issue](https://twitter.com/HolaValentin/status/507203399121924097)

### 0.5.1

* Fix [issue with exp_security_hashes table in ExpressionEngine 2.5.4](http://expressionengine.stackexchange.com/questions/1999/database-error-exp-security-hashes)

### 0.5

* Added page description textarea
* Can now clone surveys (pages & questions)
* ExpressionEngine [Multiple Site Manager (MSM)](http://expressionengine.com/downloads/details/multiple_site_manager/) support

### 0.4.1

* Question titles can now be 16777215 characters (up from 128)
* Added check for PHP version in module installer and updater (PHP 5.3 or greater is required)

### 0.4

* Better breadcrumbs
* Fixed bug related to viewing a survey by explicitly passing a survey ID
* Added ability to remove survey submissions

### 0.3.7

* Fixed undefined index bug with text and radio matrix questions when compiling survey results

### 0.3.6

* Fixed datepicker so it should now work after Ajax requests
* Now using textarea for question title
* Fixed undefined index bug with checkbox and radio questions when compiling survey results

### 0.3.5

* Added URL encoding for checkbox, radio matrix, and radio question types

### 0.3.4

* Fixed MySQL default value bug for survey questions

### 0.3.3

* Updated datepicker formating for date question type
* Fixed MySQL default value bug for survey pages

### 0.3.2

* Adding "Guests" member group to prevent PHP error on survey submissions page

### 0.3.1

* Fixed date question type issues (If you are upgrading from an earlier version you will have to open up each survey and re-save it...)
* Added "Enter-to-add" functionality for radio matrix question type

### 0.3

* Fixed compatibility issues with EE 2.3
* Using PHP DateTime object features (requires PHP 5.3 or greater)

## Beta

VWM Surveys is currently **in beta**. I by no means consider it to be production ready. That said, I welcome your feedback and suggestions on how to make VWM Surveys more awesome.

## Support (in order of preference)

I provide the following support options:

* [GitHub Wiki](https://github.com/vmichnowicz/vwm_surveys/wiki)
* [GitHub Issue Tracker](https://github.com/vmichnowicz/vwm_surveys/issues)
* [Devot:ee](http://devot-ee.com/add-ons/vwm-surveys)
* [Personal contact form](http://www.vmichnowicz.com/contact)
* [Twitter](http://twitter.com/vmichnowicz)

## Coming Soon-ish

* More question types!
* Unique checkbox, radio, and radio matrix keys
* Messages moved into language files