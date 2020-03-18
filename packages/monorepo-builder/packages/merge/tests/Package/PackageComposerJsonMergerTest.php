<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Tests\Package;

use Symplify\MonorepoBuilder\Merge\ComposerJsonMerger;

final class PackageComposerJsonMergerTest extends AbstractMergeTestCase
{
    public function test(): void
    {
        if (! defined('SYMPLIFY_MONOREPO')) {
            $this->markTestSkipped('Already tested on monorepo');
        }

        $expectedComposerJson = $this->createComposerJson(
            __DIR__ . '/PackageComposerJsonMergerSource/expected-with-relative-paths.json'
        );

        $this->doTestDirectoryMergeToFile(__DIR__ . '/Source', $expectedComposerJson);
    }

    public function testUniqueRepositories(): void
    {
        $expectedComposerJson = $this->createComposerJson(__DIR__ . '/PackageComposerJsonMergerSource/expected.json');

        $fileInfos = $this->getFileInfosFromDirectory(__DIR__ . '/SourceUniqueRepositories');

        $composerJsonMerger = self::$container->get(ComposerJsonMerger::class);
        $mergedComposerJson = $composerJsonMerger->mergeFileInfos($fileInfos);

        $this->assertNotEmpty($mergedComposerJson->getRepositories());
        $this->assertComposerJsonEquals($expectedComposerJson, $mergedComposerJson);
    }
}
