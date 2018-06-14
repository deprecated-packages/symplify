<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Git;

use Nette\Utils\Strings;
use Symfony\Component\Process\Process;

final class DateToTagResolver
{
    /**
     * @inspiration https://stackoverflow.com/a/7561599/1348344
     */
    public function resolveCommitToTag(string $commitHash): string
    {
        $process = new Process('git describe --contains ' . $commitHash);
        $process->run();

        $tag = trim($process->getOutput());

        if (empty($tag)) {
            return 'Unreleased';
        }

        // resolves formats like "v4.2.0~5^2"
        if (Strings::contains($tag, '~')) {
            return explode('~', $tag)[0];
        }

        return $tag;
    }
}
