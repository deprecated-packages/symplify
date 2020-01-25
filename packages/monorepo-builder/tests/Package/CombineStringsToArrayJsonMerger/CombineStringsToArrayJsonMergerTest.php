<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Package\CombineStringsToArrayJsonMerger;

use Symplify\MonorepoBuilder\Tests\Package\AbstractMergeTestCase;

/**
 * @see \Symplify\MonorepoBuilder\Package\PackageComposerJsonMerger
 */
final class CombineStringsToArrayJsonMergerTest extends AbstractMergeTestCase
{
    public function testIdenticalNamespaces(): void
    {
        $expectedComposerJson = $this->createComposerJson(__DIR__ . '/Source/expected.json');

        $this->doTestDirectoryMergeToFile(__DIR__ . '/../SourceIdenticalNamespaces', $expectedComposerJson);
    }
}
