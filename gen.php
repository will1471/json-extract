<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

$logger = new \Monolog\Logger(
    'logger',
    [new \Monolog\Handler\StreamHandler('php://stdout')],
    [new \Monolog\Processor\IntrospectionProcessor()]
);

$logger->debug('hello', ['foo' => 'bar']);
$logger->debug('world', ['foo' => 'bar']);
