<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Composer;

use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Composer\StaticVendorDirProvider;

final class VendorDirProviderTest extends TestCase
{
    public function testProvide(): void
    {
        $this->assertStringEndsWith('vendor', StaticVendorDirProvider::provide());

        $this->assertFileExists(StaticVendorDirProvider::provide() . '/autoload.php');
    }
}
