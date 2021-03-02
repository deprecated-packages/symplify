<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\FileSystem\ChangelogFileSystem;

use Symplify\ChangelogLinker\Console\Command\DumpMergesCommand;
use Symplify\ChangelogLinker\FileSystem\ChangelogFileSystem;
use Symplify\ChangelogLinker\HttpKernel\ChangelogLinkerKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileSystem;

final class ChangelogFileSystemTest extends AbstractKernelTestCase
{
    /**
     * @var string
     */
    private const FILE_CHANGELOG = 'tests/FileSystem/ChangelogFileSystem/Source/CHANGELOG.md';

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

    public function testReadChangeLogNotExists(): void
    {
        rename($this->changelogFileSystem->getChangelogFilePath(), $this->changelogFileSystem->getChangelogFilePath() . '.backup');

        $isExists = file_exists($this->changelogFileSystem->getChangelogFilePath());
        $this->assertFalse($isExists);

        $this->changelogFileSystem->readChangelog();

        $isExists = file_exists($this->changelogFileSystem->getChangelogFilePath());
        $this->assertTrue($isExists);

        rename($this->changelogFileSystem->getChangelogFilePath() . '.backup', $this->changelogFileSystem->getChangelogFilePath());
    }

    public function testAddToChangelogOnPlaceholder(): void
    {
        $originalContent = $this->changelogFileSystem->readChangelog();

        $this->changelogFileSystem->addToChangelogOnPlaceholder(<<<CODE_SAMPLE
## Unreleased

### Added

- [#2] Added foo- [#1] Added woo
CODE_SAMPLE
, DumpMergesCommand::CHANGELOG_PLACEHOLDER_TO_WRITE);

        $this->changelogFileSystem->addToChangelogOnPlaceholder(<<<CODE_SAMPLE
## Unreleased

### Added

- [#4] Added bar- [#3] Added baz
CODE_SAMPLE
, DumpMergesCommand::CHANGELOG_PLACEHOLDER_TO_WRITE);

        $this->changelogFileSystem->addToChangelogOnPlaceholder(<<<CODE_SAMPLE
## Unreleased

### Added

- [#6] Added y
- [#5] Added x
CODE_SAMPLE
, DumpMergesCommand::CHANGELOG_PLACEHOLDER_TO_WRITE);

        $this->changelogFileSystem->addToChangelogOnPlaceholder(<<<CODE_SAMPLE
## Unreleased

### Added

- [#32] Added xxxxxx- [#31] Added yyyyy
CODE_SAMPLE
, DumpMergesCommand::CHANGELOG_PLACEHOLDER_TO_WRITE);

        $this->changelogFileSystem->addToChangelogOnPlaceholder(<<<CODE_SAMPLE
## Unreleased

### Added

- [#35] Added xxxxxx1, Thanks to [@samsonasik]- [#34] Added yyyyy1
CODE_SAMPLE
, DumpMergesCommand::CHANGELOG_PLACEHOLDER_TO_WRITE);

        $smartFileSystem = new SmartFileSystem();

        $changelogFile = file_exists(self::FILE_CHANGELOG)
            ? self::FILE_CHANGELOG
            : 'packages/changelog-linker/' . self::FILE_CHANGELOG;
        $content = $smartFileSystem->readFile($changelogFile);

        $smartFileSystem->dumpFile($changelogFile, $originalContent);
        $expectedListData = $smartFileSystem->readFile(__DIR__ . '/Source/EXPECTED_CHANGELOG_LIST_DATA.md');

        $this->assertStringContainsString($expectedListData, $content);
    }
}
