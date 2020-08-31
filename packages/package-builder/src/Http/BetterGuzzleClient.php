<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Http;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Psr\Http\Message\ResponseInterface;

final class BetterGuzzleClient
{
    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @api
     * @return mixed[]|mixed|void
     */
    public function requestToJson(string $url): array
    {
        $request = new Request('GET', $url);
        $response = $this->client->send($request);

        if (! $this->isSuccessCode($response)) {
            throw BadResponseException::create($request, $response);
        }

        $content = (string) $response->getBody();
        if ($content === '') {
            return [];
        }

        try {
            return Json::decode($content, Json::FORCE_ARRAY);
        } catch (JsonException $jsonException) {
            throw new JsonException(
                'Syntax error while decoding:' . $content,
                $jsonException->getLine(),
                $jsonException
            );
        }
    }

    private function isSuccessCode(ResponseInterface $response): bool
    {
        return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
    }
}
