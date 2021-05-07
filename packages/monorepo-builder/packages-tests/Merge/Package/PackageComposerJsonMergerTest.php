<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Merge\Package;

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

        $this->doTestDirectoryMergeToFile(__DIR__ . '/SourceUniqueRepositories', $expectedComposerJson);
    }
}
