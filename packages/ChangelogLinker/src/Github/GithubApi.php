<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Github;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Nette\Utils\Strings;
use Psr\Http\Message\ResponseInterface;
use Symplify\ChangelogLinker\Exception\Github\GithubApiException;
use Symplify\ChangelogLinker\Guzzle\ResponseFormatter;
use Throwable;

final class GithubApi
{
    /**
     * @var string
     * @see https://developer.github.com/v3/pulls/#parameters
     * Note: per_page=100 is maximum value, results need to be collected with "&page=X"
     */
    private const URL_CLOSED_PULL_REQUESTS = 'https://api.github.com/repos/%s/pulls?state=closed&per_page=100';

    /**
     * @var string
     */
    private const URL_PULL_REQUEST_BY_ID = 'https://api.github.com/repos/%s/pulls/%d';

    /**
     * @var string
     */
    private $repositoryName;

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
        string $repositoryName,
        ResponseFormatter $responseFormatter,
        ?string $githubToken
    ) {
        $this->client = $client;
        $this->repositoryName = $repositoryName;
        $this->responseFormatter = $responseFormatter;

        // Inspired by https://github.com/weierophinney/changelog_generator/blob/master/changelog_generator.php
        if ($githubToken) {
            $this->options['headers']['Authorization'] = 'token ' . $githubToken;
        }
    }

    /**
     * @return mixed[]
     */
    public function getMergedPullRequestsSinceId(int $id, ?string $baseBranch = null): array
    {
        $pullRequests = $this->getPullRequestsSinceId($id, $baseBranch);

        $mergedPullRequests = $this->filterMergedPullRequests($pullRequests);

        // include all
        if ($id <= 1) {
            return $mergedPullRequests;
        }

        $sinceMergedAt = $this->getMergedAtByPullRequest($id);
        // include all
        if ($sinceMergedAt === null) {
            return $mergedPullRequests;
        }

        return $this->filterPullRequestsNewerThanMergedAt($mergedPullRequests, $sinceMergedAt);
    }

    public function isPullRequestMergedToBaseBranch(int $pullRequestId, string $baseBranch): bool
    {
        $json = $this->getSinglePullRequestJson($pullRequestId);
        return $json['base']['ref'] === $baseBranch;
    }

    /**
     * @return mixed[]
     */
    private function getPullRequestsSinceId(int $id, ?string $baseBranch = null): array
    {
        $maxPage = 10; // max. 1000 merge requests to dump

        $pullRequests = [];
        for ($i = 1; $i <= $maxPage; ++$i) {
            $url = sprintf(self::URL_CLOSED_PULL_REQUESTS, $this->repositoryName) . '&page=' . $i;
            if ($baseBranch !== null) {
                $url .= '&base=' . $baseBranch;
            }
            $response = $this->getResponseToUrl($url);

            // already no more pages → stop
            $newPullRequests = $this->responseFormatter->formatToJson($response);
            if (count($newPullRequests) === 0) {
                break;
            }

            $pullRequests = array_merge($pullRequests, $newPullRequests);

            // our id was found → stop after this one
            $pullRequestIds = array_column($newPullRequests, 'number');
            if (in_array($id, $pullRequestIds, true)) {
                break;
            }
        }

        return $pullRequests;
    }

    /**
     * @param mixed[] $pullRequests
     * @return mixed[]
     */
    private function filterMergedPullRequests(array $pullRequests): array
    {
        return array_filter($pullRequests, function (array $pullRequest): bool {
            return isset($pullRequest['merged_at']) && $pullRequest['merged_at'] !== null;
        });
    }

    private function getMergedAtByPullRequest(int $id): ?string
    {
        $json = $this->getSinglePullRequestJson($id);

        return $json['merged_at'] ?? null;
    }

    /**
     * @param mixed[] $pullRequests
     * @return mixed[]
     */
    private function filterPullRequestsNewerThanMergedAt(array $pullRequests, string $mergedAt): array
    {
        return array_filter($pullRequests, function (array $pullRequest) use ($mergedAt): bool {
            return $pullRequest['merged_at'] > $mergedAt;
        });
    }

    private function getResponseToUrl(string $url): ResponseInterface
    {
        try {
            $request = new Request('GET', $url);
            $response = $this->client->send($request, $this->options);
        } catch (RequestException $requestException) {
            if (Strings::contains($requestException->getMessage(), 'API rate limit exceeded')) {
                throw $this->createGithubApiTokenException('Github API rate limit exceeded.', $requestException);
            }

            // un-authorized access → provide token
            if ($requestException->getCode() === 401) {
                throw $this->createGithubApiTokenException('Github API un-authorized access.', $requestException);
            }

            throw $requestException;
        }

        if ($response->getStatusCode() !== 200) {
            throw BadResponseException::create($request, $response);
        }

        return $response;
    }

    private function createGithubApiTokenException(string $reason, Throwable $throwable): GithubApiException
    {
        $message = $reason . PHP_EOL . 'Create a token at https://github.com/settings/tokens/new with only repository scope and use it as ENV variable: "GITHUB_TOKEN=... vendor/bin/changelog-linker ..." option.';

        return new GithubApiException($message, $throwable->getCode(), $throwable);
    }

    private function getSinglePullRequestJson(int $pullRequestId): array
    {
        $url = sprintf(self::URL_PULL_REQUEST_BY_ID, $this->repositoryName, $pullRequestId);
        $response = $this->getResponseToUrl($url);
        return $this->responseFormatter->formatToJson($response);
    }
}
