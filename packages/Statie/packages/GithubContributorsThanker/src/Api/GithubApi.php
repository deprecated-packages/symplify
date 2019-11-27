<?php declare(strict_types=1);

namespace Symplify\Statie\GithubContributorsThanker\Api;

use Symplify\PackageBuilder\Http\BetterGuzzleClient;

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
     * @var BetterGuzzleClient
     */
    private $betterGuzzleClient;

    public function __construct(
        BetterGuzzleClient $betterGuzzleClient,
        string $thankerRepositoryName,
        string $thankerAuthorName
    ) {
        $this->thankerRepositoryName = $thankerRepositoryName;
        $this->thankerAuthorName = $thankerAuthorName;
        $this->betterGuzzleClient = $betterGuzzleClient;
    }

    /**
     * @return mixed[]
     */
    public function getContributors(): array
    {
        $url = sprintf(self::API_CONTRIBUTORS, $this->thankerRepositoryName);
        $json = $this->betterGuzzleClient->requestToJson($url);

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
}
