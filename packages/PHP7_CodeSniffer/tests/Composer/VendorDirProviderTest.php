<?php declare(strict_types=1);

namespace Symplify\PHP7_CodeSniffer\Tests\Composer;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Composer\VendorDirProvider;

final class VendorDirProviderTest extends TestCase
{
    public function testProvide()
    {
        $this->assertStringEndsWith('vendor', VendorDirProvider::provide());
        $this->assertStringEndsWith('vendor', VendorDirProvider::provide());

        $this->assertFileExists(VendorDirProvider::provide() . '/autoload.php');
    }
}
