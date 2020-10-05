<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Git;

use Nette\Utils\Strings;
use Symfony\Component\Process\Process;

/**
 * @see \Symplify\ChangelogLinker\Tests\Git\GitCommitDateTagResolverTest
 */
final class GitCommitDateTagResolver
{
    /**
     * @var string
     * @see https://regex101.com/r/Aggust/1
     */
    private const DATE_REGEX = '#(?<date>\d{4}-\d{2}-\d{2})#';

    /**
     * @var string
     * @see https://regex101.com/r/50201m/2
     */
    private const TAG_WITH_DATE_REGEX = '#\(?tag: (?<tag>[v.\d]+)\)#';

    /**
     * @var string[]
     */
    private $tagsToDates = [];

    /**
     * @var string[]
     */
    private $commitHashToTag = [];

    /**
     * @inspiration https://stackoverflow.com/a/6900369/1348344
     */
    public function __construct()
    {
        $datesWithTags = (array) explode(PHP_EOL, $this->getDatesWithTagsInString());

        foreach ($datesWithTags as $datesWithTag) {
            $dateMatch = Strings::match($datesWithTag, self::DATE_REGEX);
            $date = $dateMatch['date'];

            $tagMatch = Strings::match($datesWithTag, self::TAG_WITH_DATE_REGEX);
            if (! isset($tagMatch['tag'])) {
                continue;
            }

            $tag = $tagMatch['tag'];

            $this->tagsToDates[$tag] = $date;
        }
    }

    public function resolveDateForTag(string $tag): ?string
    {
        if ($tag === 'Unreleased') {
            return null;
        }

        if (isset($this->tagsToDates[$tag])) {
            return $this->tagsToDates[$tag];
        }

        return null;
    }

    /**
     * @inspiration https://stackoverflow.com/a/7561599/1348344
     */
    public function resolveCommitToTag(string $commitHash): string
    {
        if (isset($this->commitHashToTag[$commitHash])) {
            $tag = $this->commitHashToTag[$commitHash];
        } else {
            $process = new Process(['git', 'describe', '--contains', $commitHash]);
            $process->run();
            $tag = trim($process->getOutput());
            $this->commitHashToTag[$commitHash] = $tag;
        }

        if ($tag === '') {
            return 'Unreleased';
        }

        // resolves formats like "v4.2.0~5^2"
        if (Strings::contains($tag, '~')) {
            return explode('~', $tag)[0];
        }

        return $tag;
    }

    private function getDatesWithTagsInString(): string
    {
        $process = new Process(['git', 'log', '--tags', '--simplify-by-decoration', '--pretty="format:%ai %d"']);
        $process->run();

        return trim($process->getOutput());
    }
}
