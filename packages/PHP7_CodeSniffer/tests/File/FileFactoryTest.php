<?php

namespace Symplify\PHP7_CodeSniffer\Tests\File;

use PHP_CodeSniffer\Files\File as BaseFile;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Application\Fixer;
use Symplify\PHP7_CodeSniffer\Contract\File\FileInterface;
use Symplify\PHP7_CodeSniffer\File\File;
use Symplify\PHP7_CodeSniffer\File\FileFactory;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;

final class FileFactoryTest extends TestCase
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp()
    {
        $this->fileFactory = Instantiator::createFileFactory();
    }

    public function testCreate()
    {
        $file = $this->fileFactory->create(__DIR__ . '/FileFactorySource/SomeFile.php', false);
        $this->assertInstanceOf(File::class, $file);
        $this->assertInstanceOf(BaseFile::class, $file);
        $this->assertInstanceOf(FileInterface::class, $file);
        $this->assertInstanceOf(Fixer::class, $file->fixer);
    }

    /**
     * @expectedException \Nette\FileNotFoundException
     */
    public function testCreateFromNotFile()
    {
        $this->fileFactory->create(__DIR__, false);
    }
}
