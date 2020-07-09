<?php

declare(strict_types=1);

use React\Socket\ConnectionInterface;
use React\Socket\TcpConnector;

require_once __DIR__ . '/../vendor/autoload.php';

if ($argc < 3) {
    throw new RuntimeException('Port must be specified');
}

$address = (string) $argv[1];
$id = (string) $argv[2];

$loop = \React\EventLoop\Factory::create();
$tcpConector = new TcpConnector($loop);
$tcpConector->connect($address)->then(function (ConnectionInterface $connection) use ($loop, $id): void {

    // $connection->pipe(new React\Stream\WritableResourceStream(STDOUT, $loop));
    $connection->write('hello');

    $connection->on('data', function($data) use ($connection, $id) {
        if ($data === 'analyse') {
            sleep(1);
            $connection->write('exit');
        }
    });

});

$loop->run();
