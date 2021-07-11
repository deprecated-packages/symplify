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
        $easyCodingStandardKernel = new EasyCodingStandardKernel('foo', false);

        $this->assertSame($easyCodingStandardKernel->getCacheDir(), $easyCodingStandardKernel->getCacheDir());
    }

    public function testPurgesCacheDirOnBoot(): void
    {
        $easyCodingStandardKernel = new EasyCodingStandardKernel('foo', false);

        $dummyFile = $easyCodingStandardKernel->getCacheDir() . '/dummy';
        FileSystem::write($dummyFile, '');

        $this->assertFileExists($dummyFile);
        $easyCodingStandardKernel->boot();
        $this->assertFileDoesNotExist($dummyFile);

        FileSystem::delete($easyCodingStandardKernel->getCacheDir());
    }
}
