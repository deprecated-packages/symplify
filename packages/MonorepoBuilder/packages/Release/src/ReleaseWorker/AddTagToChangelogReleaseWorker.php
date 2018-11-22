<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use function Safe\getcwd;

final class AddTagToChangelogReleaseWorker implements ReleaseWorkerInterface
{
    public function getPriority(): int
    {
        return 500;
    }

    public function work(Version $version): void
    {
        $changelogFilePath = getcwd() . '/CHANGELOG.md';
        if (! file_exists($changelogFilePath)) {
            return;
        }

        $newHeadline = '## ' . $version->getVersionString() . ' - ' . (new DateTime())->format('Y-m-d');

        $changelogFileContent = FileSystem::read($changelogFilePath);
        $changelogFileContent = Strings::replace($changelogFileContent, '#\#\# Unreleased#', $newHeadline);

        FileSystem::write($changelogFilePath, $changelogFileContent);
    }

    public function getDescription(Version $version): string
    {
        return 'Change "Unreleased" in `CHANGELOG.md` to new version + today date';
    }
}
