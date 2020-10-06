<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\FileSystem\ChangelogFileSystem;

use Symplify\ChangelogLinker\FileSystem\ChangelogFileSystem;
use Symplify\ChangelogLinker\HttpKernel\ChangelogLinkerKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileSystem;

final class ChangelogFileSystemTest extends AbstractKernelTestCase
{
    /**
     * @var ChangelogFileSystem
     */
    private $changelogFileSystem;

    protected function setUp(): void
    {
        if (defined('SYMPLIFY_MONOREPO')) {
            $this->bootKernelWithConfigs(ChangelogLinkerKernel::class, [__DIR__ . '/config/test_config.yaml']);
        } else {
            $this->bootKernelWithConfigs(ChangelogLinkerKernel::class, [__DIR__ . '/config/test_config_split.yaml']);
        }

        $this->changelogFileSystem = self::$container->get(ChangelogFileSystem::class);
    }

    public function testAddToChangelogOnPlaceholder(): void
    {
        $originalContent = $this->changelogFileSystem->readChangelog();

        $this->changelogFileSystem->addToChangelogOnPlaceholder('## Unreleased - [#1] Added foo', '## Unreleased');
        $this->changelogFileSystem->addToChangelogOnPlaceholder('## Unreleased - [#2] Added bar', '## Unreleased');

        $fileChangelog = 'tests/FileSystem/ChangelogFileSystem/Source/CHANGELOG.md';
        $changelogFile = file_exists($fileChangelog)
            ? $fileChangelog
            : 'packages/changelog-linker/' . $fileChangelog;

        $smartFileSystem = new SmartFileSystem();
        $content = $smartFileSystem->readFile($changelogFile);
        $this->assertStringContainsString(
            $smartFileSystem->readFile(__DIR__ . '/Source/EXPECTED_CHANGELOG_LIST_DATA.md'),
            $content
        );

        $smartFileSystem->dumpFile($changelogFile, $originalContent);
    }
}
