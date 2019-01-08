<?php declare(strict_types=1);

namespace Symplify\Statie\GithubContributorsThanker\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use Symplify\Statie\GithubContributorsThanker\Guzzle\ResponseFormatter;
use function Safe\rsort;
use function Safe\sprintf;

final class GithubApi
{
    /**
     * Better detailed URL - the more than top 30
     * @see https://developer.github.com/v3/repos/statistics/#get-contributors-list-with-additions-deletions-and-commit-counts
     * @var string
     */
    private const API_CONTRIBUTORS = 'https://api.github.com/repos/%s/stats/contributors';

    /**
     * @var string
     */
    private $thankerRepositoryName;

    /**
     * @var string
     */
    private $thankerAuthorName;

    /**
     * @var mixed[]
     */
    private $options = [];

    /**
     * @var Client
     */
    private $client;

    /**
     * @var ResponseFormatter
     */
    private $responseFormatter;

    public function __construct(
        Client $client,
        ResponseFormatter $responseFormatter,
        string $thankerRepositoryName,
        string $thankerAuthorName,
        ?string $githubToken
    ) {
        $this->client = $client;
        $this->responseFormatter = $responseFormatter;
        $this->thankerRepositoryName = $thankerRepositoryName;
        $this->thankerAuthorName = $thankerAuthorName;

        if ($githubToken) {
            $this->options['headers']['Authorization'] = 'token ' . $githubToken;
        }
    }

    /**
     * @return mixed[]
     */
    public function getContributors(): array
    {
        $url = sprintf(self::API_CONTRIBUTORS, $this->thankerRepositoryName);
        $json = $this->callRequestToJson($url);

        // reverse to max → min
        rsort($json);

        $contributors = [];
        foreach ($json as $item) {
            // skip ego
            if ($item['author']['login'] === $this->thankerAuthorName) {
                continue;
            }

            $contributors[] = [
                'name' => $item['author']['login'],
                'url' => $item['author']['html_url'],
                'photo' => $item['author']['avatar_url'],
                'contribution_count' => $item['total'],
            ];
        }

        return $contributors;
    }

    /**
     * @return mixed[]
     */
    private function callRequestToJson(string $url): array
    {
        $request = new Request('GET', $url);
        $response = $this->client->send($request, $this->options);

        if ($response->getStatusCode() >= 300) {
            throw BadResponseException::create($request, $response);
        }

        return $this->responseFormatter->formatResponseToJson($response, $url);
    }
}
