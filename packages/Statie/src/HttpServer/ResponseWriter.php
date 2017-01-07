<?php

declare(strict_types=1);

namespace Symplify\Statie\HttpServer;

use React\Http\Request;
use React\Http\Response;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\Statie\HttpServer\MimeType\MimeTypeDetector;

final class ResponseWriter
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var MimeTypeDetector
     */
    private $mimeTypeDetector;

    public function __construct(OutputInterface $output, MimeTypeDetector $mimeTypeDetector)
    {
        $this->output = $output;
        $this->mimeTypeDetector = $mimeTypeDetector;
    }

    public function send404Response(Request $request, Response $response)
    {
        $this->writeResponse(404, $request->getPath());
        $response->writeHead(404, [
            'Content-Type' => 'text/html',
        ]);

        $response->end(
            '<h1>404 - Not Found</h1>' .
            '<p>Statie web server could not find the requested resource.</p>'
        );
    }

    public function send200Response(Request $request, Response $response, string $path)
    {
        $response->writeHead(200, [
            'Content-Type' => $this->mimeTypeDetector->detectForFilename($path),
        ]);

        $this->writeResponse(200, $request->getPath());
        $response->end(file_get_contents($path));
    }

    private function writeResponse(int $responseCode, string $path)
    {
        $message = sprintf(
            'Response code "%s" for path: "%s"',
            $responseCode,
            $path
        );

        $this->output->writeln($this->prepareResponseMessage($responseCode, $message));
    }

    private function prepareResponseMessage(int $responseCode, string $message) : string
    {
        $wrapOpen = '<info>';
        $wrapClose = '</info>';
        if ($responseCode >= 400) {
            $wrapOpen = '<error>';
            $wrapClose = '</error>';
        }

        return $wrapOpen . $message . $wrapClose;
    }
}
