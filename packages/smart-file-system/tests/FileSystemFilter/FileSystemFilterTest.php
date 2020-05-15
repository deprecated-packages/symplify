<?php

declare(strict_types=1);

namespace Symplify\SmartFileSystem\Tests\FileSystemFilter;

use PHPStan\Testing\TestCase;
use Symplify\SmartFileSystem\FileSystemFilter;

final class FileSystemFilterTest extends TestCase
{
    /**
     * @var FileSystemFilter
     */
    private $fileSystemFilter;

    protected function setUp(): void
    {
        $this->fileSystemFilter = new FileSystemFilter();
    }

    public function testSeparateFilesAndDirectories(): void
    {
        $sources = [__DIR__, __DIR__ . '/FileSystemFilterTest.php'];

        [$files, $directories] = $this->fileSystemFilter->separateFilesAndDirectories($sources);

        $this->assertCount(1, $files);
        $this->assertCount(1, $directories);
    }
}
