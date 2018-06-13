<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Github;

use GuzzleHttp\Client;
use Nette\Utils\Json;
use Psr\Http\Message\ResponseInterface;
use Symplify\ChangelogLinker\Exception\Github\GithubApiException;

final class GithubApi
{
    /**
     * @var string
     */
    private const URL_PULL_REQUESTS = 'https://api.github.com/repos/%s/pulls?state=all';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $repositoryName;

    /**
     * @var mixed[]
     */
    private $options = [];

    /**
     * @todo guess from "composer.json" if not filled
     */
    public function __construct(Client $client, string $repositoryName)
    {
        $this->client = $client;
        $this->repositoryName = $repositoryName;
    }

    /**
     * @return mixed[]
     */
    public function getClosedPullRequestsSinceId(int $id): array
    {
        $url = sprintf(self::URL_PULL_REQUESTS, $this->repositoryName);

        $response = $this->getResponseToUrl($url);

        $result = $this->createJsonArrayFromResponse($response);

        return $this->filterOutPullRequestsWithIdLesserThan($result, $id);
    }

    /**
     * @return mixed[]
     */
    private function createJsonArrayFromResponse(ResponseInterface $response): array
    {
        return Json::decode((string) $response->getBody(), Json::FORCE_ARRAY);
    }

    private function getResponseToUrl(string $url): ResponseInterface
    {
        $response = $this->client->request('GET', $url, $this->options);

        if ($response->getStatusCode() !== 200) {
            throw new GithubApiException(sprintf(
                'Response to GET request "%s" failed: "%s"',
                $url,
                $response->getReasonPhrase()
            ));
        }

        return $response;
    }

    /**
     * @param mixed[] $pullRequests
     * @return mixed[]
     */
    private function filterOutPullRequestsWithIdLesserThan(array $pullRequests, int $id): array
    {
        return array_filter($pullRequests, function (array $pullRequest) use ($id) {
            return $pullRequest['number'] > $id;
        });
    }

    /**
     * Inspired by https://github.com/weierophinney/changelog_generator/blob/master/changelog_generator.php
     */
    public function authorizeToken(string $token): void
    {
        $this->options['headers']['Authorization'] = 'token ' . $token;
    }
}
