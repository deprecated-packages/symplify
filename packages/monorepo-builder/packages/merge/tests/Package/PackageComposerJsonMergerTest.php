<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Tests\Package;

use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;

final class PackageComposerJsonMergerTest extends AbstractMergeTestCase
{
    public function test(): void
    {
        $expectedComposerJson = $this->createExpectedComposerJson();

        $this->doTestDirectoryMergeToFile(__DIR__ . '/Source', $expectedComposerJson);
    }

    public function testUniqueRepositories(): void
    {
        $expectedComposerJson = $this->createComposerJson(__DIR__ . '/PackageComposerJsonMergerSource/expected.json');

        $this->doTestDirectoryMergeToFile(__DIR__ . '/SourceUniqueRepositories', $expectedComposerJson);
    }

    private function createExpectedComposerJson(): ComposerJson
    {
        if (defined('SYMPLIFY_MONOREPO')) {
            return $this->createComposerJson(
                __DIR__ . '/PackageComposerJsonMergerSource/expected-with-relative-paths.json'
            );
        }

        return $this->createComposerJson(
            __DIR__ . '/PackageComposerJsonMergerSource/split-expected-with-relative-paths.json'
        );
    }
}
