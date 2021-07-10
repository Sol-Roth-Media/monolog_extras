<?php

namespace Drupal\monolog_extras\Logger\Handler;

use Drupal\monolog_extras\Event\MonologExtrasDrupalLoggerEvent;
use Drupal\Core\Logger\RfcLogLevel;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Monolog\Formatter\NormalizerFormatter;
use Monolog\Formatter\FormatterInterface;

/**
 * Forwards logs to a Drupal logger.
 */
class DrupalExtrasHandler extends AbstractProcessingHandler {

  private $logger;
  /**
   * Event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  private static $levels = [
    Logger::DEBUG => RfcLogLevel::DEBUG,
    Logger::INFO => RfcLogLevel::INFO,
    Logger::NOTICE => RfcLogLevel::NOTICE,
    Logger::WARNING => RfcLogLevel::WARNING,
    Logger::ERROR => RfcLogLevel::ERROR,
    Logger::CRITICAL => RfcLogLevel::CRITICAL,
    Logger::ALERT => RfcLogLevel::ALERT,
    Logger::EMERGENCY => RfcLogLevel::EMERGENCY,
  ];

  /**
   * Constructs a Default object.
   *
   * @param \Psr\Log\LoggerInterface $wrapped
   *   The wrapped Drupal logger.
   * @param bool|int $level
   *   The minimum logging level at which this handler will be triggered.
   * @param bool $bubble
   *   Whether the messages that are handled can bubble up the stack or not.
   */
  public function __construct(LoggerInterface $wrapped, $level = Logger::DEBUG, $bubble = TRUE) {
    parent::__construct($level, $bubble);
    $this->logger = $wrapped;
    $this->eventDispatcher =\Drupal::service('event_dispatcher'); // TODO Replace with proper dependency injection.

  }

  public function mapMonologToDrupalLogger($record){
    $context = $record['context'] + [
      'channel' => $record['channel'],
      'link' => '',
      'user' => isset($record['extra']['user']) ? $record['extra']['user'] : NULL,
      'uid' => isset($record['extra']['uid']) ? $record['extra']['uid'] : 0,
      'request_uri' => isset($record['extra']['request_uri']) ? $record['extra']['request_uri'] : '',
      'referer' => isset($record['extra']['referer']) ? $record['extra']['referer'] : '',
      'ip' => isset($record['extra']['ip']) ? $record['extra']['ip'] : 0,
      'timestamp' => $record['datetime']->format('U'),
    ];
    return $context;
  }

  /**
   * {@inheritDoc}
   */
  protected function getDefaultFormatter(): FormatterInterface
  {
    return new NormalizerFormatter('U');
  }

  /**
   * {@inheritdoc}
   */
  public function write(array $record): void {
    // Set up context with the data Drupal loggers expect.
    // @see Drupal\Core\Logger\LoggerChannel::log()
    $formatter = $this->getFormatter();
    $formatter->setJsonPrettyPrint(true);
    $context = $this->mapMonologToDrupalLogger($record);

    $extras = json_encode($record['extra'], JSON_PRETTY_PRINT);
    $record['message'] .= '<br /><br /><pre>'. $extras . '</pre>';
    $level = static::$levels[$record['level']];
    $event = new MonologExtrasDrupalLoggerEvent($level, $record, $context);
    $this->eventDispatcher->dispatch($event);
    $this->logger->log($level, $record['message'], $context);
  }

}
