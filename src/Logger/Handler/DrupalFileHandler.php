<?php

namespace Drupal\monolog_extras\Logger\Handler;

use \Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Logger\RfcLogLevel;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Psr\Log\LoggerInterface;
use Monolog\Formatter\NormalizerFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Formatter\FormatterInterface;

/**
 * Forwards logs to a Drupal logger.
 */
class DrupalFileHandler extends AbstractProcessingHandler
{

  private $logger;
  /**
   * Event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  //protected $eventDispatcher;

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
  public function __construct($level = Logger::DEBUG, $bubble = TRUE)
  {
    parent::__construct($level, $bubble);
  }

  public function mapMonologToDrupalLogger($record)
  {
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
  // protected function getDefaultFormatter(): FormatterInterface
  // {
  // return new NormalizerFormatter('U');
  //$formatter = new LineFormatter();
  //$formatter->setJsonPrettyPrint(true);
  //return $formatter;
  // }

  /**
   * {@inheritdoc}
   */
  public function handle(array $record): bool
  {
    //TODO Remove hardcoded check and add a custom method call based on channel.
    if ($level == 4 || $level == 5 || $record['level_name'] == "WARNING" || $record['level_name'] == "NOTICE") {
      return TRUE;
    } else {
      parent::handle($record);
    }
  }


  /**
   * {@inheritdoc}
   */
  public function write(array $record): void
  {
    $context = $this->mapMonologToDrupalLogger($record);
    $level = static::$levels[$record['level']];
    /** @var \Drupal\Core\File\FileSystemInterface $file_system */
    $file_system = \Drupal::service('file_system');
    $directory = 'private://logs';
    $file_name = 'drupal.log';
    $uri = $directory . '/' . $file_name;
    $real_path = $file_system->realpath($uri);

    if (!file_exists($real_path)) { // create it if it doesn't exist.
      $file_system->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY);
      $file_result = $file_system->saveData($record['formatted'], $uri, EXISTS_REPLACE);
    } else { // append data to it.
      // TODO add nice line formatter here.
      $file_result = file_put_contents(
        $real_path,
        $record['formatted'],
        FILE_APPEND
      );
    }
  }

}
