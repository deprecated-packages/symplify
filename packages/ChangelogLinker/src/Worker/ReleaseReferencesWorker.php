<?php declare(strict_types=1);

namespace Symplify\ChangelogLinker\Worker;

use Nette\Utils\DateTime;
use Nette\Utils\Strings;
use Symplify\ChangelogLinker\Contract\Worker\WorkerInterface;

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

        // @todo, maybe this release is still WIP, so check if $lastTag already exists in the Content

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
