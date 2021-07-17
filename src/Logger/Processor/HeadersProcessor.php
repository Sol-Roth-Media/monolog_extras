<?php

namespace Drupal\monolog_extras\Logger\Processor;
use Drupal\monolog\Logger\Processor\AbstractRequestProcessor;

/**
 * Class HeadersProcessor
 */
class HeadersProcessor extends AbstractRequestProcessor {

  /**
   * {@inheritdoc}
   */
  public function __invoke(array $record): array
  {
    if ($request = $this->getRequest()) {
      $record['extra']['headers'] = $request->headers->all();
    }
    return $record;
  }

}
