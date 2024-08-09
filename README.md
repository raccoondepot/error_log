[![ErrorLog](https://img.shields.io/badge/beta-v11.5.4-green)](github.com/raccoondepot/error_log/tree/11.5.4) [![TYPO3 11](https://img.shields.io/badge/TYPO3-11-orange.svg)](https://get.typo3.org/version/11) [![License](https://img.shields.io/github/license/TYPO3-Documentation/tea)]() [![RD](https://img.shields.io/badge/Raccoon-Depot-50b99a)](https://raccoondepot.com) [![StandWithUkraine](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/badges/StandWithUkraine.svg)](https://stand-with-ukraine.pp.ua/)

# TYPO3 Advanced Error Log

[![Stand With Ukraine](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/banner-direct-team.svg)](https://stand-with-ukraine.pp.ua)

## Features
- Handle TYPO3 errors and content object exceptions
- Handle the errors occurred before TYPO3 is fully loaded
- Display grouped errors in the TYPO3 backend module
- Provide detailed information for each error occurrence, including stack traces
- Configurable error notifications via email and Slack
- Configurable combined error reports via email and Slack
- AI assistance for error resolution


## Requirements
Basic requirements are the same as for the TYPO3 version it used with, however some adjustments in php.ini might be needed to collect arguments for error stack trace.

Starting from `PHP v7.4`, a new ini directive `zend.exception_ignore_args` with default value `On` was added. As result, there are no arguments collected with default PHP setup. It should be changed to `zend.exception_ignore_args = Off`.

## Installation
You can install Advanced error log with 
```bash
composer require rd/error-log
```
After the extension is installed, the Upgrade Wizard rewrites the project\'s LocalConfiguration file with a setup for our exception handlers.


## Configuration
Most of the settings are available over *Settings* action in module. It consists of two tabs - `Settings` and `Users`. In the Settings tab there are three sections - `General` - , `Slack` and `OpenAI`, which help you to configure corresponding features. Each section starts with a checkbox which enables the current part of the setup. Users tab shows the list of users who enabled the email notification.
![](https://cp-dev.raccoondepot.com/imgs/el_0.jpg)

### General
With the `Enable Error log feature` checkbox you can disable/enable the logging. It writes changes to LocalConfiguration the same way as the Upgrade Wizard does.

Field `Number of days before error expires` is responsible for error log database cleanup. It says when we need to remove the outdated records. The default value `0` means that error records are not deleting.

### Slack
To use Slack we need to enable it with a corresponding checkbox (`Enable Slack notifications`). To send notifications over Slack, a Slack application should be created first. The process of creating one is described on [Slack tutorial page](https://api.slack.com/tutorials/tracks/actionable-notifications).

When the application is ready, you should add its authorization token and a channel ID where the notifications will be sent. Channel ID could be seen on the bottom of the Channel details popup. Fields `Slack authorization token` and `Slack channel ID` should be filled with these values.

There\'s a `Send test message` button to check if the application is configured correctly.

Next step is to configure the notifications for error occurrences and reports. This could be done with `Error reports sending` and `Error occurrence notification sending` dropdowns.

If configured correctly, Slack notifications will appear as follows::
![](https://cp-dev.raccoondepot.com/imgs/el_1.jpg)

### OpenAI
OpenAI API is used to get the suggestions about error cause and resolving. To use OpenAI API, authorization token should be obtained from [OpenAI](https://platform.openai.com/) and entered into the `OpenAI authorization token` field. In the `OpenAI system pre-prompt` field it is possible to adjust the initial instruction to LLM. Dropdown `OpenAI GPT model` gives you a possibility to select which model to use. gpt-3.5-turbo is the cheapest and gpt-4-turbo is the smartest one, but a bit slower and the most expensive.

In the bottom of the OpenAI section there\'s a `Test AI` button to check if everything is configured properly.

### Email notifications
There\'s no configuration for email notifications in extension\'s module. They are configuring separately for each user (if needed), so these options could be found in either User settings or Backend users module (user editing is also available from *Users* tab of our extension\'s Settings action). 

In both User settings and Backend users there\'s a new tab *Error Notifications* added. Here you can find a configuration, similar to the one used for Slack - `Enable email notifications` checkbox and two dropdowns: `Error reports sending` and `Error occurrence notification sending`.

To make it work, the user email field should also be filled in user settings. If everything is configured, users will receive the emails like this:
![](https://cp-dev.raccoondepot.com/imgs/el_2.jpg)

### Scheduler task
Our extension uses a scheduler task *Error log service manager task*. It is used for sending reports, process errors which weren\'t properly dispatched (those that appeared earlier than TYPO3 was fully loaded) and cleans the database up. It should be enough to execute it once or twice per hour.

### Content object exceptions
By default we override the native content object exception handler to write content object errors details into our log, but the rest of its behavior is the same (*Oops, an error occurred* message in the section which couldn\'t be rendered). If you want to see the error details on the page, you should turn off this handler over typoscript:
```
config.contentObjectExceptionHandler = 0
```
As result the error details will be displayed on page, but they still will be logged with our extension


## Main functionality description
The main part of our extension is the `Error log` module which is placed in the `System` section of the TYPO3 backend.

The main view shows the filtered list of grouped errors:
![](https://cp-dev.raccoondepot.com/imgs/el_3.jpg)

You can click on any error group to see the list of all the errors in this group:
![](https://cp-dev.raccoondepot.com/imgs/el_4.jpg)

Each individual error in the list could be expanded with detailed information about it. Here you can see a table with details like entry point, browser information, IP, etc. and a full stack trace with arguments:
![](https://cp-dev.raccoondepot.com/imgs/el_5.jpg)

If OpenAI is configured there will also be a tab `AI Assistant` with a prompt based on error details ready to be executed. It is also possible to modify the prompt if needed. Request from AI will be rendered below the textarea with prompt:
![](https://cp-dev.raccoondepot.com/imgs/el_6.jpg)


## Events and features extending
In our extension we dispatching two events:

Event | Description | Parameters
------------- | ------------- | -------------
ErrorEvent | Triggered when either error occurred or when non-dispatched error found during scheduler task execution. | $error, $isFirstOccurrence
ReportEvent | Happens when it\'s time to render a report for user email or Slack. | $frequency, $errors

You can easily add your own listener to any of those events and perform your own actions when it is dispatched.


## Future plans
This extension is a work in progress and we have a lot of ideas to implement in future versions. Here are some of them:
- Handle errors from PHP error log file (either directly or with directly uploaded file);
- Dynamic configuration for alternative notification sources;
- Use retrieval augmented generation (RAG) for giving AI better TYPO3 context.