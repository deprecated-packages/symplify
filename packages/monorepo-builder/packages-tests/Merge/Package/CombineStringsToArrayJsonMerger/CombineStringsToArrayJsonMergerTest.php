<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Merge\Package\CombineStringsToArrayJsonMerger;

use Symplify\MonorepoBuilder\Tests\Merge\Package\AbstractMergeTestCase;

final class CombineStringsToArrayJsonMergerTest extends AbstractMergeTestCase
{
    public function testIdenticalNamespaces(): void
    {
        if (! defined('SYMPLIFY_MONOREPO')) {
            $this->markTestSkipped('Already tested on monorepo');
        }

        $expectedComposerJson = $this->createComposerJson(__DIR__ . '/Source/expected.json');

        $this->doTestDirectoryMergeToFile(__DIR__ . '/../SourceIdenticalNamespaces', $expectedComposerJson);
    }
}
