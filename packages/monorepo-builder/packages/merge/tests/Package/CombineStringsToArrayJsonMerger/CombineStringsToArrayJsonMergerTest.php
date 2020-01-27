<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Merge\Tests\Package\CombineStringsToArrayJsonMerger;

use Symplify\MonorepoBuilder\ComposerJsonObject\ValueObject\ComposerJson;
use Symplify\MonorepoBuilder\Merge\Tests\Package\AbstractMergeTestCase;

final class CombineStringsToArrayJsonMergerTest extends AbstractMergeTestCase
{
    public function testIdenticalNamespaces(): void
    {
        if (! defined('SYMPLIFY_MONOREPO')) {
            $this->markTestSkipped('Already tested on monorepo');
        }

        $expectedComposerJson = $this->getExpectedComposerJson();

        $this->doTestDirectoryMergeToFile(__DIR__ . '/../SourceIdenticalNamespaces', $expectedComposerJson);
    }

    private function getExpectedComposerJson(): ComposerJson
    {
        return $this->createComposerJson(__DIR__ . '/Source/expected.json');
    }
}
