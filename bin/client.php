<?php

declare(strict_types=1);

use Clue\React\NDJson\Decoder;
use Clue\React\NDJson\Encoder;
use React\Socket\ConnectionInterface;
use React\Socket\TcpConnector;

require_once __DIR__ . '/../vendor/autoload.php';

if ($argc < 3) {
    throw new RuntimeException('Address and process identifier must be specified, usage: `php client.php <address> <identifier>`');
}

$address = (string) $argv[1];
$id = (string) $argv[2];

$loop = \React\EventLoop\Factory::create();
$tcpConector = new TcpConnector($loop);
$tcpConector->connect($address)->then(function (ConnectionInterface $serverConnection) use ($id): void {
    $out = new Encoder($serverConnection);
    $in = new Decoder($serverConnection, true);

    echo '[CLIENT ' . $id . ']: Sending hello' . PHP_EOL;

    $out->write([
        'action' => 'hello',
        'id' => $id,
    ]);

    $in->on('data', function(array $data) use ($out, $id) {
        if ($data['action'] !== 'analyse') {
            return;
        }

        echo '[CLIENT ' . $id . ']: Received analyse request' . PHP_EOL;

        sleep(1);

        // LOGIC here

        $out->write([
            'action' => 'result',
            'id' => $id,
            'results' => $data['files'],
        ]);
    });
});

$loop->run();
