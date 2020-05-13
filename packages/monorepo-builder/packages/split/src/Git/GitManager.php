<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Split\Git;

use Nette\Utils\Strings;
use Symplify\MonorepoBuilder\Release\Process\ProcessRunner;

final class GitManager
{
    /**
     * @var string[]
     */
    private const COMMITER_DATE_COMMAND = ['git', 'tag', '-l', '--format="%(committerdate)"'];

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

        return preg_match('#^\s*$#', $result) !== false;
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
        // Do nothing if it is null or an empty string.
        if (empty($this->githubToken)) {
            return $remoteRepository;
        }

        [, $partAfterAt,
        ] = explode('@', $remoteRepository, 2);
        $partAfterAt = Strings::replace($partAfterAt, '#:#', '/');

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
