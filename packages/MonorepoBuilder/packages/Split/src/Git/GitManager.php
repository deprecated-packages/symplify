<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Git;

use Nette\Utils\Strings;
use Symfony\Component\Process\Process;

final class GitManager
{
    /**
     * @var null|string
     */
    private $githubToken;

    public function __construct(?string $githubToken)
    {
        $this->githubToken = $githubToken;
    }

    public function getMostRecentTag(string $gitDirectory): string
    {
        $process = new Process('git tag -l --sort=committerdate', $gitDirectory);
        $process->run();

        $tags = $process->getOutput();
        $tagList = explode(PHP_EOL, trim($tags));

        return (string) array_pop($tagList);
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

        [, $partAfterAt] = explode('@', $remoteRepository, 2);
        $partAfterAt = Strings::replace($partAfterAt, '#:#', '/');

        return sprintf('https://%s@%s', $this->githubToken, $partAfterAt);
    }
}
