<?php

declare(strict_types=1);

use Clue\React\NDJson\Decoder;
use Clue\React\NDJson\Encoder;
use React\ChildProcess\Process;

require_once __DIR__ . '/../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

// 0 will create local socket server with random available port
$server = new \React\Socket\TcpServer(0, $loop);

$processes = [];
$results = [];

$server->on('connection', function (React\Socket\ConnectionInterface $clientConnection) use (&$results) {
    $in = new Decoder($clientConnection, true);
    $out = new Encoder($clientConnection);

    $in->on('data', function (array $data) use ($out) {
        if ($data['action'] !== 'hello') {
            return;
        }

        echo '[SERVER] Received hello from: ' . $data['id'] . PHP_EOL;

        $out->write([
            'action' => 'analyse',
            'files' => [
                uniqid() . '.php',
                uniqid() . '.php',
                uniqid() . '.php',
            ],
        ]);
    });

    $in->on('data', function (array $data) use ($clientConnection, &$results) {
        if ($data['action'] !== 'result') {
            return;
        }

        echo '[SERVER] Received results from ' . $data['id'] . PHP_EOL;
        print_r($data['results']);

        $results = array_merge($results, $data['results']);

        $clientConnection->close();
    });
});

// Starting children processes
for($i=0 ; $i<=2 ; $i++) {
    $id = uniqid();
    $command = 'php bin/client.php ' . $server->getAddress() . ' ' . $id;
    $childProcess = new Process($command, null, null, []);
    $processes[$id] = $id;

    $childProcess->on('exit', function ($exitCode) use ($server, &$processes, $id) {
        echo $id . ': ' . $exitCode . PHP_EOL;
        unset($processes[$id]);

        if (count($processes) === 0) {
            echo '[SERVER] No more processes, closing server' . PHP_EOL;
            $server->close();
        }
    });

    $childProcess->start($loop);
}

$loop->run();

echo '[MAIN PROCESS] Collected results:' . PHP_EOL;
print_r($results);
