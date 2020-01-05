<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\PackageComposerFinder;

use PHPUnit\Framework\TestCase;
use Symplify\MonorepoBuilder\PackageComposerFinder;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;

final class PackageComposerFinderTest extends TestCase
{
    public function test(): void
    {
        /** @var PackageComposerFinder $packageComposerFinder */
        $packageComposerFinder = new PackageComposerFinder([__DIR__ . '/Source'], new FinderSanitizer());
        $this->assertCount(2, $packageComposerFinder->getPackageComposerFiles());
    }
}
