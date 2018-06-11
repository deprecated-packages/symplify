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
        // for local testing
        if (file_exists('temp.txt')) {
            return unserialize(file_get_contents('temp.txt'));
        }

        $url = sprintf(self::URL_PULL_REQUESTS, $this->repositoryName);

        $response = $this->getResponseToUrl($url);

        $result = $this->createJsonArrayFromResponse($response);

        file_put_contents('temp.txt', serialize($result));

        return $this->filterOutPullRequestsWithIdLesserThen($result, $id);
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
        $response = $this->client->request('GET', $url);

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
    private function filterOutPullRequestsWithIdLesserThen(array $pullRequests, int $id): array
    {
        return array_filter($pullRequests, function (array $pullRequest) use ($id) {
            return $pullRequest['number'] > $id;
        });
    }
}
