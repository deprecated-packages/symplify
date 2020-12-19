<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\FileSystem\ChangelogFileSystem;

use Symplify\ChangelogLinker\FileSystem\ChangelogFileSystem;
use Symplify\ChangelogLinker\HttpKernel\ChangelogLinkerKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileSystem;
use Symplify\ChangelogLinker\Console\Command\DumpMergesCommand;

final class ChangelogFileSystemTest extends AbstractKernelTestCase
{
    /**
     * @var ChangelogFileSystem
     */
    private $changelogFileSystem;

    protected function setUp(): void
    {
        if (defined('SYMPLIFY_MONOREPO')) {
            $this->bootKernelWithConfigs(ChangelogLinkerKernel::class, [__DIR__ . '/config/test_config.php']);
        } else {
            $this->bootKernelWithConfigs(ChangelogLinkerKernel::class, [__DIR__ . '/config/test_config_split.php']);
        }

        $this->changelogFileSystem = $this->getService(ChangelogFileSystem::class);
    }

    public function testAddToChangelogOnPlaceholder(): void
    {
        $originalContent = $this->changelogFileSystem->readChangelog();

        $this->changelogFileSystem->addToChangelogOnPlaceholder(<<<CONTENT
## Unreleased

### Added

- [#1] Added foo
CONTENT, DumpMergesCommand::CHANGELOG_PLACEHOLDER_TO_WRITE);

        $this->changelogFileSystem->addToChangelogOnPlaceholder(<<<CONTENT
## Unreleased

### Added

- [#2] Added bar
CONTENT, DumpMergesCommand::CHANGELOG_PLACEHOLDER_TO_WRITE);

        $fileChangelog = 'tests/FileSystem/ChangelogFileSystem/Source/CHANGELOG.md';
        $smartFileSystem = new SmartFileSystem();

        $changelogFile = file_exists($fileChangelog)
            ? $fileChangelog
            : 'packages/changelog-linker/' . $fileChangelog;
        $content          = $smartFileSystem->readFile($changelogFile);
        $expectedListData = $smartFileSystem->readFile(__DIR__ . '/Source/EXPECTED_CHANGELOG_LIST_DATA.md');

        $smartFileSystem->dumpFile($changelogFile, $originalContent);

        $this->assertStringContainsString(
            $expectedListData,
            $content
        );
    }
}
