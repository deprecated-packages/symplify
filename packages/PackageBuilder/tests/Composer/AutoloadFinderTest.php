<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Composer;

use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Composer\AutoloadFinder;

final class AutoloadFinderTest extends TestCase
{
    public function test(): void
    {
        $this->assertSame(
            __DIR__ . '/AutoloadFinderSource/vendor/autoload.php',
            AutoloadFinder::findNearDirectories([__DIR__ . '/AutoloadFinderSource/src'])
        );

        $this->assertSame(
            __DIR__ . '/AutoloadFinderSource/vendor/autoload.php',
            AutoloadFinder::findNearDirectories([__DIR__ . '/AutoloadFinderSource'])
        );
    }
}
