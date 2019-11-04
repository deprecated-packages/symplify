<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\FileSystem;

use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\Exception\FileSystem\DirectoryNotFoundException;
use Symplify\PackageBuilder\Exception\FileSystem\FileNotFoundException;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

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

        $this->assertStringEndsWith($smartFileInfo->getRelativePath(), __DIR__);
        $this->assertStringEndsWith($smartFileInfo->getRelativePathname(), __FILE__);
    }

    public function testRelativeToDir(): void
    {
        $smartFileInfo = new SmartFileInfo(__DIR__ . '/SmartFileInfoSource/AnotherFile.txt');

        $this->assertSame(
            'SmartFileInfoSource/AnotherFile.txt',
            $smartFileInfo->getRelativeFilePathFromDirectory(__DIR__)
        );
    }

    public function testRelativeToDirException(): void
    {
        $smartFileInfo = new SmartFileInfo(__FILE__);

        $this->expectException(DirectoryNotFoundException::class);
        $smartFileInfo->getRelativeFilePathFromDirectory('non-existing-path');
    }
}
