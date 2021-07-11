<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\HttpKernel;

use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;

final class EasyCodingStandardKernelTest extends TestCase
{
    public function testCacheDirIsConsistentAcrossCalls(): void
    {
        $kernel = new EasyCodingStandardKernel('foo', false);

        $this->assertSame($kernel->getCacheDir(), $kernel->getCacheDir());
    }

    public function testPurgesCacheDirOnBoot(): void
    {
        $kernel = new EasyCodingStandardKernel('foo', false);

        $dummyFile = $kernel->getCacheDir() . '/dummy';
        FileSystem::write($dummyFile, '');

        $this->assertFileExists($dummyFile);
        $kernel->boot();
        $this->assertFileDoesNotExist($dummyFile);

        FileSystem::delete($kernel->getCacheDir());
    }
}
