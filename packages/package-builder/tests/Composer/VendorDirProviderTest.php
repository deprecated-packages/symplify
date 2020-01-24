<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Composer;

use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Composer\VendorDirProvider;

final class VendorDirProviderTest extends TestCase
{
    public function testProvide(): void
    {
        $this->assertStringEndsWith('vendor', VendorDirProvider::provide());

        $this->assertFileExists(VendorDirProvider::provide() . '/autoload.php');
    }
}
