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
        $this->bootKernelWithConfigs(ChangelogLinkerKernel::class, [__DIR__ . '/config/test_config.yaml']);
        $this->changelogFileSystem = self::$container->get(ChangelogFileSystem::class);
    }

    public function testAddToChangelogOnPlaceholder(): void
    {
        $changelogFile = file_exists(
            'packages/changelog-linker/tests/FileSystem/ChangelogFileSystem/Source/CHANGELOG.md'
        )
            ? 'packages/changelog-linker/tests/FileSystem/ChangelogFileSystem/Source/CHANGELOG.md'
            : 'tests/FileSystem/ChangelogFileSystem/Source/CHANGELOG.md';

        $smartFileSystem = new SmartFileSystem();
        $originalContent = $smartFileSystem->readFile($changelogFile);

        $this->changelogFileSystem->addToChangelogOnPlaceholder('## Unreleased - [#1] Added foo', '## Unreleased');
        $this->changelogFileSystem->addToChangelogOnPlaceholder('## Unreleased - [#2] Added bar', '## Unreleased');

        $content = $smartFileSystem->readFile($changelogFile);

        $regexFunction = method_exists($this, 'assertMatchesRegularExpression')
            ? 'assertMatchesRegularExpression'
            : 'assertRegExp';

        $this->{$regexFunction}(
            <<<CODE_SAMPLE
#\#\# Unreleased

\<\!-- dumped content start --\>
 - \[\#2\] Added bar\<\!-- dumped content end --\>

\<!-- dumped content start --\>
 - \[\#1\] Added foo\<\!-- dumped content end --\>

\[\#1\]: https:\/\/github\.com\/.*\/symplify\/pull\/1
\[\#2\]: https:\/\/github\.com\/.*\/symplify\/pull\/2#
CODE_SAMPLE
            ,
            $content
        );

        $smartFileSystem->dumpFile($changelogFile, $originalContent);
    }
}
