<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\FileSystem;

use PHPUnit\Framework\TestCase;
use Symplify\PackageBuilder\FileSystem\FileSystem;

final class FileSystemTest extends TestCase
{
    /**
     * @var FileSystem
     */
    private $fileSystem;

    protected function setUp(): void
    {
        $this->fileSystem = new FileSystem();
    }

    public function testSeparateFilesAndDirectories(): void
    {
        $sources = [__DIR__, __DIR__ . '/FileSystemTest.php'];

        [$files, $directories] = $this->fileSystem->separateFilesAndDirectories($sources);

        $this->assertCount(1, $files);
        $this->assertCount(1, $directories);
    }
}
