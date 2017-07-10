<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector\Tests;

use Nette\Utils\FileSystem;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Tests\AbstractContainerAwareTestCase;

final class ChangedFilesDetectorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ChangedFilesDetector
     */
    private $changedFilesDetector;

    protected function setUp(): void
    {
        FileSystem::createDir($this->getCacheDirectory());

        $this->changedFilesDetector = $this->container->get(ChangedFilesDetector::class);
        $this->changedFilesDetector->changeConfigurationFile(
            __DIR__ . '/ChangedFilesDetectorSource/easy-coding-standard.neon'
        );
    }

    protected function tearDown(): void
    {
        FileSystem::delete($this->getCacheDirectory());
    }

    public function testAddFile(): void
    {
        $this->assertTrue($this->changedFilesDetector->hasFileChanged(
            __DIR__ . '/ChangedFilesDetectorSource/OneClass.php'
        ));

        $this->assertFalse($this->changedFilesDetector->hasFileChanged(
            __DIR__ . '/ChangedFilesDetectorSource/OneClass.php')
        );
    }

    public function testHasFileChanged(): void
    {
        $this->changedFilesDetector->addFile(__DIR__ . '/ChangedFilesDetectorSource/OneClass.php');

        $this->assertFalse($this->changedFilesDetector->hasFileChanged(
            __DIR__ . '/ChangedFilesDetectorSource/OneClass.php')
        );
    }

    public function testInvalidateCacheOnConfigurationChange(): void
    {
        $phpFile = __DIR__ . '/ChangedFilesDetectorSource/OneClass.php';
        $this->changedFilesDetector->addFile($phpFile);

        $this->assertFalse($this->changedFilesDetector->hasFileChanged($phpFile));

        $this->changedFilesDetector->changeConfigurationFile(
            __DIR__ . '/ChangedFilesDetectorSource/another-configuration.neon'
        );

        $this->assertTrue($this->changedFilesDetector->hasFileChanged($phpFile));
        $this->assertFalse($this->changedFilesDetector->hasFileChanged($phpFile));
    }

    private function getCacheDirectory(): string
    {
        return __DIR__ . '/cache';
    }
}
