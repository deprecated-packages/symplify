<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\HttpServer;

use Mimey\MimeTypes;
use PHPUnit\Framework\TestCase;
use React\Http\Request;
use React\Http\Response;
use React\Tests\Http\ConnectionStub;
use Symfony\Component\Console\Output\BufferedOutput;
use Symplify\Statie\HttpServer\MimeType\MimeTypeDetector;
use Symplify\Statie\HttpServer\ResponseWriter;

final class ResponseWriterTest extends TestCase
{
    /**
     * @var string
     */
    private $someFilePath = __DIR__ . '/ResponseWriterSource/someFile.txt';

    /**
     * @var BufferedOutput
     */
    private $bufferedOutput;

    /**
     * @var ResponseWriter
     */
    private $responseWriter;

    protected function setUp()
    {
        $this->bufferedOutput = new BufferedOutput();
        $mimeTypeDetector = new MimeTypeDetector(new MimeTypes());
        $this->responseWriter = new ResponseWriter($this->bufferedOutput, $mimeTypeDetector);
    }

    public function testSend200Response()
    {
        $request = new Request('GET', '/');

        $connectionStub = new ConnectionStub();
        $response = new Response($connectionStub);

        $this->responseWriter->send200Response($request, $response, $this->someFilePath);

        $this->assertContains('HTTP/1.1 200 OK', $connectionStub->getData());
        $this->assertContains('Content-Type: text/plain', $connectionStub->getData());
        $this->assertContains(file_get_contents($this->someFilePath), $connectionStub->getData());

        $this->assertContains('Response code "200" for path: "/"', $this->bufferedOutput->fetch());
    }

    public function testSend400Response()
    {
        $request = new Request('GET', '/missing');

        $connectionStub = new ConnectionStub();
        $response = new Response($connectionStub);

        $this->responseWriter->send404Response($request, $response);

        $this->assertContains('HTTP/1.1 404 Not Found', $connectionStub->getData());
        $this->assertContains('Content-Type: text/html', $connectionStub->getData());
        $this->assertContains(
            '<h1>404 - Not Found</h1><p>Statie web server could not find the requested resource.</p>',
            $connectionStub->getData()
        );

        $this->assertContains('Response code "404" for path: "/missing"', $this->bufferedOutput->fetch());
    }
}
