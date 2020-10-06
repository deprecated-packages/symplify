<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\FileSystem\ChangelogFileSystem;

use Iterator;
use Symplify\ChangelogLinker\FileSystem\ChangelogFileSystem;
use Symplify\ChangelogLinker\HttpKernel\ChangelogLinkerKernel;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class ChangelogFileSystemTest extends AbstractKernelTestCase
{
    /**
     * @var changelogFileSystem
     */
    private $changelogFileSystem;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(ChangelogLinkerKernel::class, [__DIR__ . '/config/test_config.yaml']);
        $this->changelogFileSystem = self::$container->get(ChangelogFileSystem::class);
    }

    public function testAddToChangelogOnPlaceholder(): void
    {
        $smartFileSystem = new SmartFileSystem();
        $originalContent = $smartFileSystem->readFile('packages/changelog-linker/tests/FileSystem/ChangelogFileSystem/Source/CHANGELOG.md');

        $this->changelogFileSystem->addToChangelogOnPlaceholder('## Unreleased - [#1] Added foo', '## Unreleased');
        $this->changelogFileSystem->addToChangelogOnPlaceholder('## Unreleased - [#2] Added bar', '## Unreleased');

        $expected = $smartFileSystem->readFile('packages/changelog-linker/tests/FileSystem/ChangelogFileSystem/Source/CHANGELOG.md');

        $this->assertSame(
            $expected,
            <<<CONTENT
## Unreleased

<!-- dumped content start -->
- [#2] Added bar<!-- dumped content end -->

<!-- dumped content start -->
- [#1] Added foo<!-- dumped content end -->

[#1]: https://github.com/samsonasik/symplify/pull/1
[#2]: https://github.com/samsonasik/symplify/pull/2

CONTENT
        );

        $smartFileSystem->dumpFile('packages/changelog-linker/tests/FileSystem/ChangelogFileSystem/Source/CHANGELOG.md', $originalContent);
    }
}
