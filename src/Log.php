<?php declare (strict_types = 1);
namespace memCrab\Log;

use Aws\Sqs\SqsClient;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\SqsHandler;
use Monolog\Formatter\JsonFormatter;
use Monolog\Logger;

/**
 *  Log for core project
 *
 *  @author Oleksandr Diudiun
 */
class Log {

    private static $instance;

    function __construct() {}

    public static function stream($name) {
        if (!isset(self::$instance[$name])) {
            self::$instance[$name] = new Logger($name);
        }

        return self::$instance[$name];
    }

    public static function setDefaultRotationHandler($name) {
        self::stream($name)->pushHandler(new RotatingFileHandler('logs/' . $name . '/' . $name . '.log'));
    }

	public static function setSqsHandler(SqsClient $sqsClient, $queueUrl, $name) {
		$stream = new SqsHandler($sqsClient, $queueUrl);
		$stream->setFormatter(new JsonFormatter());
		self::stream($name)->pushHandler($stream);
	}
}
