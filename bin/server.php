<?php

declare(strict_types=1);

use React\ChildProcess\Process;

require_once __DIR__ . '/../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

// 0 will create local socket server with random available port
$server = new \React\Socket\TcpServer(0, $loop);

$processes = [];

$server->on('connection', function (React\Socket\ConnectionInterface $connection) use ($loop) {
    $connection->on('data', function ($data) use ($connection, $loop) {
        echo $connection->getRemoteAddress() . ': ' . $data . PHP_EOL;

        if ($data === 'exit') {
            $connection->close();
        }

        if ($data === 'hello') {
            // $connection->pipe(new React\Stream\WritableResourceStream(STDOUT, $loop));
            $connection->write('analyse');
        }
    });
});

for($i=0 ; $i<=2 ; $i++) {
    $id = uniqid();
    $command = 'php bin/client.php ' . $server->getAddress() . ' ' . $id;
    $process = new Process($command, null, null, []);
    $process->start($loop);
    $processes[$id] = $id;

    $process->on('exit', function ($exitcode) use ($server, &$processes, $id) {
        unset($processes[$id]);

        if (count($processes) === 0) {
            echo 'No more processes, closing server' . PHP_EOL;
            $server->close();
        }
    });
}

$loop->run();
