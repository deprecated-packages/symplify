<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\HttpServer;

use Mimey\MimeTypes;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;
use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Configuration\Parser\NeonParser;
use Symplify\Statie\HttpServer\HttpServer;
use Symplify\Statie\HttpServer\MimeType\MimeTypeDetector;
use Symplify\Statie\HttpServer\ResponseWriter;

final class HttpServerTest extends TestCase
{
    /**
     * @var HttpServer
     */
    private $httpServer;

    protected function setUp()
    {
        $output = new NullOutput;
        $configuration = new Configuration(new NeonParser);
        $configuration->setOutputDirectory('outputDirectory');
        $mimeTypeDetector = new MimeTypeDetector(new MimeTypes);
        $this->httpServer = new HttpServer($configuration, $output, new ResponseWriter($output, $mimeTypeDetector));
    }

    public function test()
    {
        $this->httpServer->init();

        $this->httpServer->addPeriodicTimer(5, function () {
        });
    }
}
