<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();
$server = new \React\Socket\TcpServer(0, $loop);

$server->on('connection', function (React\Socket\ConnectionInterface $connection) use ($loop) {
    $connection->pipe(new React\Stream\WritableResourceStream(STDOUT, $loop));
    $connection->write("Hello " . $connection->getRemoteAddress() . "!\n");
    $connection->write("Welcome to this amazing server!\n");
    $connection->write("Here's a tip: don't say anything.\n");

    $connection->on('data', function ($data) use ($connection) {
        echo $data . "\n";
        $connection->close();
    });
});

$server->on('error', function (Exception $e) {
    echo 'Error' . $e->getMessage() . PHP_EOL;
});

echo $server->getAddress() . "\n";

$loop->run();
