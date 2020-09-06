<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Composer;

use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Composer\VendorDirProvider;

final class VendorDirProviderTest extends TestCase
{
    public function testProvide(): void
    {
        $vendorDirProvider = new VendorDirProvider();
        $this->assertStringEndsWith('vendor', $vendorDirProvider->provide());

        $this->assertFileExists($vendorDirProvider->provide() . '/autoload.php');
    }
}
