<?php

declare(strict_types=1);

use React\Socket\ConnectionInterface;
use React\Socket\TcpConnector;

require_once __DIR__ . '/../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

$tcpConector = new TcpConnector($loop);
$tcpConector->connect('tcp://127.0.0.1:55330')->then(function (ConnectionInterface $connection) use ($loop): void {
    $connection->pipe(new React\Stream\WritableResourceStream(STDOUT, $loop));
    $connection->write("Hello World!\n");
});

$loop->run();
