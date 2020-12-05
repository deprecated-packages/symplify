<?php

declare(strict_types=1);

namespace Symplify\VendorPatches\Tests\Json;

use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileSystem;
use Symplify\VendorPatches\HttpKernel\VendorPatchesKernel;
use Symplify\VendorPatches\Json\JsonFileSystem;

final class JsonFileSystemTest extends AbstractKernelTestCase
{
    /**
     * @var JsonFileSystem
     */
    private $jsonFileSystem;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    protected function setUp(): void
    {
        self::bootKernel(VendorPatchesKernel::class);
        $this->jsonFileSystem = $this->getService(JsonFileSystem::class);
        $this->smartFileSystem = $this->getService(SmartFileSystem::class);
    }

    public function testLoadFilePathToJson(): void
    {
        $json = $this->jsonFileSystem->loadFilePathToJson(__DIR__ . '/JsonFileSystemSource/some_file.json');
        $this->assertSame([
            'key' => 'value',
        ], $json);
    }

    public function testWriteJsonToFilePath(): void
    {
        $filePath = __DIR__ . '/JsonFileSystemSource/temp_file.json';

        $this->jsonFileSystem->writeJsonToFilePath([
            'hey' => 'you',
        ], $filePath);
        $this->assertFileExists($filePath);

        $expectedFilePath = __DIR__ . '/JsonFileSystemSource/expected_temp_file.json';
        $this->assertFileEquals($expectedFilePath, $filePath);

        $this->smartFileSystem->remove($filePath);
    }
}
