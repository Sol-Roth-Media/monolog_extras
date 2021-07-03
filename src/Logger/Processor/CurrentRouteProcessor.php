<?php

namespace Drupal\monolog_extras\Logger\Processor;

/**
 * Class CurrentRouteProcessor.
 */
class CurrentRouteProcessor {

  /**
   * Constructs a Default object.
   *
   */
  public function __construct() {

  }

  /**
   * {@inheritdoc}
   */
  public function __invoke(array $record) {

    $record['extra']['route'] = \Drupal::routeMatch()->getRouteName();
    $record['extra']['uri'] = \Drupal::request()->getRequestUri();
    $record['extra']['path'] = \Drupal::service('path.current')->getPath();

    return $record;
  }

}
