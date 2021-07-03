# Monolog Extras Module

Adds extra processors (and others) to use in your drupal monolog logs.

It currently contains:
Processors:
* CurrentRouteProcessor - Adds current routename path and URI to logs 

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
