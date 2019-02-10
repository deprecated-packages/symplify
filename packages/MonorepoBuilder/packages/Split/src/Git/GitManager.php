<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Git;

use Nette\Utils\Strings;
use Symplify\MonorepoBuilder\Release\Process\ProcessRunner;

final class GitManager
{
    /**
     * @var string|null
     */
    private $githubToken;

    /**
     * @var ProcessRunner
     */
    private $processRunner;

    public function __construct(ProcessRunner $processRunner, ?string $githubToken)
    {
        $this->processRunner = $processRunner;
        $this->githubToken = $githubToken;
    }

    /**
     * Returns null, when there are no local tags yet
     */
    public function getMostRecentTag(string $gitDirectory): ?string
    {
        $command = ['git', 'tag', '-l', '--sort=committerdate'];

        if (getcwd() !== $gitDirectory) {
            $command[] = '--git-dir';
            $command[] = $gitDirectory;
        }

        $tags = $this->processRunner->run($command);
        $tagList = explode(PHP_EOL, trim($tags));

        /** @var string $theMostRecentTag */
        $theMostRecentTag = array_pop($tagList);

        if (empty($theMostRecentTag)) {
            return null;
        }

        return $theMostRecentTag;
    }

    /**
     * See https://gist.github.com/willprice/e07efd73fb7f13f917ea#file-push-sh-L15
     * see https://stackoverflow.com/a/18936804/1348344
     *
     * Before:
     * git@github.com:vendor/name.git
     *
     * After:
     * https://GITHUB_USER_NAME:SECRET_TOKEN@github.com/vendor/package-name.git
     * https://SECRET_TOKEN@github.com/vendor/package-name.git
     */
    public function completeRemoteRepositoryWithGithubToken(string $remoteRepository): string
    {
        if ($this->githubToken === null) {
            return $remoteRepository;
        }

        [, $partAfterAt,
        ] = explode('@', $remoteRepository, 2);
        $partAfterAt = Strings::replace($partAfterAt, '#:#', '/');

        return sprintf('https://%s@%s', $this->githubToken, $partAfterAt);
    }
}
