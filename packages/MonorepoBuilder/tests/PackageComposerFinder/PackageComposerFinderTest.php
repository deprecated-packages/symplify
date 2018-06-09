<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\PackageComposerFinder;

use PHPUnit\Framework\TestCase;
use Symplify\MonorepoBuilder\PackageComposerFinder;

final class PackageComposerFinderTest extends TestCase
{
    /**
     * @var PackageComposerFinder
     */
    private $packageComposerFinder;

    protected function setUp(): void
    {
        $this->packageComposerFinder = new PackageComposerFinder([__DIR__ . '/Source']);
    }

    public function test(): void
    {
        $this->assertCount(2, $this->packageComposerFinder->getPackageComposerFiles());
    }
}
