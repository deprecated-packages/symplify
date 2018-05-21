<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;
use Symplify\ChangelogLinker\Regex\RegexPattern;

final class ReleaseReferencesWorker implements WorkerInterface
{
    /**
     * @var string
     */
    private const UNRELEASED_PATTERN = '#\#\#\s+Unreleased#';

    public function processContent(string $content, string $repositoryLink): string
    {
        $unreleasedMatch = Strings::match($content, self::UNRELEASED_PATTERN);

        if (empty($unreleasedMatch)) {
            return $content;
        }

        // get last tagged version
        $lastTag = exec('git describe --abbrev=0 --tags');
        // no tag
        if (empty($lastTag)) {
            return $content;
        }

        $lastTagInContentMatch = Strings::match($content, '#\#\# \[' . RegexPattern::VERSION . '\]#');
        if ($lastTagInContentMatch) {
            // current tag version was already published
            if (version_compare($lastTag, $lastTagInContentMatch['version']) === -1) {
                return $content;
            }
        }

        // get tagged version date
        $lastTagDate = exec('git log -1 --format=%ai ' . $lastTag);
        $lastTagDateTime = DateTime::from($lastTagDate);

        return Strings::replace($content, self::UNRELEASED_PATTERN, function (array $match) use (
            $lastTag,
            $lastTagDateTime
        ) {
            return '## ' . $lastTag . ' - ' . $lastTagDateTime->format('Y-m-d');
        });
    }
}
