<?php

namespace Drupal\monolog_extras\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Defines event that is fired directly before the Drupal logger is called from the Drupal extras monolog handler.
 *
 * @package Drupal\monolog_extras\Event
 */
class MonologExtrasDrupalLoggerEvent extends Event{

  const EVENT_NAME = 'monolog_extras_drupal_logger';

  public $level;
  public $record;
  public $context;

  /**
   * Monolog Logging Watchdog constructor.
   * @param GuzzleHttp\Command\Guzzle\GuzzleClient $client
   * @param GuzzleHttp\Command\Command $command
   */
  public function __construct($level, $record, $context) {
    $this->level = $level;
    $this->record = $record;
    $this->context = $context;
  }

}
