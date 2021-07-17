# Monolog Extras Module

Adds extra processors (and others) to use in your drupal monolog logs.

It currently contains:
Processors:
* CurrentRouteProcessor - Adds current routename path and URI to logs
* Headers Processor - Adds http headers to log messages.
* Current User Operations - Adds user profile link to log messages

Handlers:
* DrupalFileHandler - Logs all messages to a private file.
* DrupalUserFile - creates a seperate log file depending on the current user and puts them in a the private files.
* DrupalExtrasHandler - Alternative Logger for monolog to handle and preprocess logs before sending them to the registered Drupal loggers (dblog for example).It adds the extra record information provided by the monolog processors and makes those avialable in the message string, so they will show up in the drupal loggers.

### Dependencies
[Monolog Drupal Module](https://www.drupal.org/project/monolog)

### Different Processors per Channel
NOTE: If you want to use different processors per channel you'll see need this patch to the Drupal monolog module - https://www.drupal.org/project/monolog/issues/3087818

```parameters:
monolog.channel_handlers:
my_channel:
handlers: ['what_failure']
formatter: 'logstash'
processors: ['current_user', 'some_channel_specific_processor', 'ip', 'referer']``
