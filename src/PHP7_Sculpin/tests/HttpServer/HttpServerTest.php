<?php

declare(strict_types=1);

namespace Symplify\PHP7_Sculpin\Tests\HttpServer;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;
use Symplify\PHP7_Sculpin\HttpServer\HttpServer;
use Symplify\PHP7_Sculpin\HttpServer\ResponseWriter;

final class HttpServerTest extends TestCase
{
    /**
     * @var HttpServer
     */
    private $httpServer;

    protected function setUp()
    {
        $output = new NullOutput();
        $this->httpServer = new HttpServer('outputDirectory', $output, new ResponseWriter($output));
    }

    public function test()
    {
        $this->httpServer->init();

        $this->httpServer->addPeriodicTimer(5, function () {
        });
    }
}
