<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Github;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Nette\Utils\Json;
use Nette\Utils\Strings;
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
        $result = $this->filterOutUnmergedPullRequests($result);

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
        try {
            $response = $this->client->request('GET', $url, $this->options);
        } catch (RequestException $requestException) {
            if (Strings::contains($requestException->getMessage(), 'API rate limit exceeded')) {
                throw new GithubApiException(
                    'Github API rate limit exceeded. Create a token at https://github.com/settings/tokens/new with only repository scope and use it in "--token TOKEN" option.'
                );
            }

            throw $requestException;
        }

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
     * @param mixed[] $pullRequests
     * @return mixed[]
     */
    private function filterOutUnmergedPullRequests(array $pullRequests): array
    {
        return array_filter($pullRequests, function (array $pullRequest) {
            return isset($pullRequest['merged_at']);
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
