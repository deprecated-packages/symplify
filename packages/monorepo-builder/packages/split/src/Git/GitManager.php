<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Git;

use Nette\Utils\Strings;
use Symplify\MonorepoBuilder\Release\Process\ProcessRunner;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class GitManager
{
    /**
     * @var string[]
     */
    private const COMMITER_DATE_COMMAND = ['git', 'tag', '-l', '--format="%(committerdate)"'];

    /**
     * @var string
     * @see https://regex101.com/r/GQv9tA/1
     */
    private const COMMITER_DATE_START_REGEX = '#^\s*$#';

    /**
     * @var string
     * @see https://regex101.com/r/gfpBgt/1
     */
    private const SEMICOLON_REGEX = '#:#';

    /**
     * @var string
     */
    private $githubToken;

    /**
     * @var ProcessRunner
     */
    private $processRunner;

    public function __construct(ProcessRunner $processRunner, ParameterProvider $parameterProvider)
    {
        $this->processRunner = $processRunner;
        $this->githubToken = (string) $parameterProvider->provideStringParameter(Option::GITHUB_TOKEN);
    }

    public function doesBranchExistOnRemote(string $branch): bool
    {
        $command = ['git', 'branch', '-a'];
        $result = $this->processRunner->run($command);

        return Strings::contains($result, sprintf('remotes/origin/%s', $branch));
    }

    public function getCurrentBranch(): string
    {
        $command = ['git', 'rev-parse', '--abbrev-ref', 'HEAD'];
        $currentBranch = trim($this->processRunner->run($command));

        if ($currentBranch === 'HEAD') {
            // Default to master when HEAD (e.g Travis)
            $currentBranch = 'master';
        }

        return $currentBranch;
    }

    public function pushBranchToRemoteOrigin(string $branch): string
    {
        $command = ['git', 'push', '--set-upstream', 'origin', trim($branch)];

        return $this->processRunner->run($command);
    }

    public function doTagsHaveCommitterDate(): bool
    {
        $result = $this->processRunner->run(self::COMMITER_DATE_COMMAND);

        return (bool) Strings::match($result, self::COMMITER_DATE_START_REGEX);
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

        $tagList = $this->parseTags($this->processRunner->run($command));

        /** @var string $theMostRecentTag */
        $theMostRecentTag = (string) array_pop($tagList);

        if ($theMostRecentTag === '') {
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
        // Do nothing if it is null or an empty string.
        if ($this->githubToken === '') {
            return $remoteRepository;
        }

        [, $partAfterAt,
        ] = explode('@', $remoteRepository, 2);
        $partAfterAt = Strings::replace($partAfterAt, self::SEMICOLON_REGEX, '/');

        return sprintf('https://%s@%s', $this->githubToken, $partAfterAt);
    }

    /**
     * @return string[]
     */
    private function parseTags(string $commandResult): array
    {
        $tags = trim($commandResult);

        // Remove all "\r" chars in case the CLI env like the Windows OS.
        // Otherwise (ConEmu, git bash, mingw cli, e.g.), leave as is.
        $tags = str_replace("\r", '', $tags);

        return explode("\n", $tags);
    }
}
