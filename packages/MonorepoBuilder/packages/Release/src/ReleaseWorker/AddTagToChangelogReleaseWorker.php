<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\ReleaseWorker;

use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use PharIo\Version\Version;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;

final class AddTagToChangelogReleaseWorker implements ReleaseWorkerInterface
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    public function getPriority(): int
    {
        return 500;
    }

    public function work(Version $version, bool $isDryRun): void
    {
        $changelogFilePath = getcwd() . '/CHANGELOG.md';
        if (! file_exists($changelogFilePath)) {
            return;
        }

        $newHeadline = '## ' . $version->getVersionString() . ' - ' . (new DateTime())->format('Y-m-d');

        $this->symfonyStyle->note(
            sprintf('Replacing "## Unreleased" headline in CHANGELOG.md with "%s"', $newHeadline)
        );

        $changelogFileContent = FileSystem::read($changelogFilePath);
        $changelogFileContent = Strings::replace($changelogFileContent, '#\#\# Unreleased#', $newHeadline);

        FileSystem::write($changelogFilePath, $changelogFileContent);

        $this->symfonyStyle->success('Done!');
    }
}
