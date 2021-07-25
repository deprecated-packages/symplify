<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Git;

use Symplify\MonorepoBuilder\Release\Process\ProcessRunner;

final class MostRecentTagResolver
{
    public function __construct(
        private ProcessRunner $processRunner
    ) {
    }

    /**
     * Returns null, when there are no local tags yet
     */
    public function resolve(string $gitDirectory): ?string
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
     * @return string[]
     */
    private function parseTags(string $commandResult): array
    {
        $tags = trim($commandResult);

        // Remove all "\r" chars in case the CLI env like the Windows OS.
        // Otherwise (ConEmu, git bash, mingw cli, e.g.), leave as is.
        $normalizedTags = str_replace("\r", '', $tags);

        return explode("\n", $normalizedTags);
    }
}
