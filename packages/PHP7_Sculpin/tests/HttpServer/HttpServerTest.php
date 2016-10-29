<?php

declare(strict_types=1);

namespace Symplify\PHP7_Sculpin\Tests\HttpServer;

use Mimey\MimeTypes;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;
use Symplify\PHP7_Sculpin\Configuration\Configuration;
use Symplify\PHP7_Sculpin\Configuration\Parser\NeonParser;
use Symplify\PHP7_Sculpin\HttpServer\HttpServer;
use Symplify\PHP7_Sculpin\HttpServer\MimeType\MimeTypeDetector;
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
        $configuration = new Configuration(new NeonParser());
        $configuration->setOutputDirectory('outputDirectory');
        $mimeTypeDetector = new MimeTypeDetector(new MimeTypes());
        $this->httpServer = new HttpServer($configuration, $output, new ResponseWriter($output, $mimeTypeDetector));
    }

    public function test()
    {
        $this->httpServer->init();

        $this->httpServer->addPeriodicTimer(5, function () {
        });
    }
}
