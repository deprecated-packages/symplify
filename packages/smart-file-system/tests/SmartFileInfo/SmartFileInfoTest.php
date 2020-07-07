<?php

declare(strict_types=1);

namespace Symplify\SmartFileSystem\Tests\SmartFileInfo;

use PHPUnit\Framework\TestCase;
use Symplify\SmartFileSystem\Exception\DirectoryNotFoundException;
use Symplify\SmartFileSystem\Exception\FileNotFoundException;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SmartFileInfoTest extends TestCase
{
    public function testInvalidPath(): void
    {
        $this->expectException(FileNotFoundException::class);
        new SmartFileInfo('random');
    }

    public function testRelatives(): void
    {
        $smartFileInfo = new SmartFileInfo(__FILE__);

        $this->assertNotSame($smartFileInfo->getRelativePath(), $smartFileInfo->getRealPath());

        $this->assertStringEndsWith(
            $this->normalizePath($smartFileInfo->getRelativePath()),
            $this->normalizePath(__DIR__)
        );
        $this->assertStringEndsWith(
            $this->normalizePath($smartFileInfo->getRelativePathname()),
            $this->normalizePath(__FILE__)
        );
    }

    public function testRealPathWithoutSuffix(): void
    {
        $smartFileInfo = new SmartFileInfo(__DIR__ . '/Source/AnotherFile.txt');

        $this->assertStringEndsWith(
            'tests/SmartFileInfo/Source/AnotherFile',
            $smartFileInfo->getRealPathWithoutSuffix()
        );
    }

    public function testRelativeToDir(): void
    {
        $smartFileInfo = new SmartFileInfo(__DIR__ . '/Source/AnotherFile.txt');

        $this->assertSame('Source/AnotherFile.txt', $smartFileInfo->getRelativeFilePathFromDirectory(__DIR__));
    }

    public function testRelativeToDirException(): void
    {
        $smartFileInfo = new SmartFileInfo(__FILE__);

        $this->expectException(DirectoryNotFoundException::class);
        $smartFileInfo->getRelativeFilePathFromDirectory('non-existing-path');
    }

    /**
     * Normalizing required to allow running tests on windows.
     */
    private function normalizePath($path): string
    {
        return str_replace('\\', '/', $path);
    }
}
