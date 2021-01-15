<?php

declare(strict_types=1);

namespace Symplify\SmartFileSystem\Tests\SmartFileSystem;

use PHPUnit\Framework\TestCase;
use Symplify\SmartFileSystem\SmartFileSystem;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SmartFileSystemTest extends TestCase
{
    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    protected function setUp(): void
    {
        $this->smartFileSystem = new SmartFileSystem();
    }

    public function testReadFileToSmartFileInfo(): void
    {
        $this->assertInstanceof(
            SmartFileInfo::class,
            $this->smartFileSystem->readFileToSmartFileInfo(__DIR__ . '/Source/file.txt')
        );
    }
}
