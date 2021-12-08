<?php

declare(strict_types=1);

namespace Symplify\NeonConfigDumper\Tests\Application\NeonConfigDumper;

use Symplify\NeonConfigDumper\Application\NeonConfigDumper;
use Symplify\NeonConfigDumper\Kernel\NeonConfigDumperKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class NeonConfigDumperTest extends AbstractKernelTestCase
{
    private NeonConfigDumper $neonConfigDumper;

    protected function setUp(): void
    {
        $this->bootKernel(NeonConfigDumperKernel::class);
        $this->neonConfigDumper = $this->getService(NeonConfigDumper::class);
    }

    public function test(): void
    {
        $neonContentFile = $this->neonConfigDumper->generate(__DIR__ . '/Fixture');
        $this->assertNotNull($neonContentFile);

        $this->assertStringEqualsFile(__DIR__ . '/Fixture/expected_services.neon', $neonContentFile);
    }
}
