<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Tests\Finder;

use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\VendorPatches\Finder\OldToNewFilesFinder;
use Symplify\VendorPatches\HttpKernel\VendorPatchesKernel;

final class VendorFilesFinderTest extends AbstractKernelTestCase
{
    /**
     * @var OldToNewFilesFinder
     */
    private $vendorFilesFinder;

    protected function setUp(): void
    {
        self::bootKernel(VendorPatchesKernel::class);

        $this->vendorFilesFinder = $this->getService(OldToNewFilesFinder::class);
    }

    public function test(): void
    {
        $files = $this->vendorFilesFinder->find(__DIR__ . '/VendorFilesFinderSource');

        $this->assertCount(1, $files);
    }
}
