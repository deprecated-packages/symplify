<?php declare(strict_types=1);

namespace Symplify\PHP7_CodeSniffer\Tests\File;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_Assert;
use Symplify\PHP7_CodeSniffer\DI\ContainerFactory;
use Symplify\PHP7_CodeSniffer\File\File;
use Symplify\PHP7_CodeSniffer\File\FileFactory;
use Symplify\PHP7_CodeSniffer\Report\ErrorDataCollector;

final class FileTest extends TestCase
{
    /**
     * @var File
     */
    private $file;

    protected function setUp()
    {
        $container = (new ContainerFactory())->create();
        $fileFactory = $container->getByType(FileFactory::class);
        $this->file = $fileFactory->create(__DIR__ . '/FileFactorySource/SomeFile.php', false);
    }

    public function testErrorDataCollector()
    {
        /** @var ErrorDataCollector $errorDataCollector */
        $errorDataCollector = PHPUnit_Framework_Assert::getObjectAttribute(
            $this->file,
            'errorDataCollector'
        );
        $this->assertSame(0, $errorDataCollector->getErrorCount());

        $this->file->addError('Some Error', 0, 'code');
        $this->assertSame(1, $errorDataCollector->getErrorCount());
        $this->assertSame(0, $errorDataCollector->getFixableErrorCount());

        $this->file->addFixableError('Some Other Error', 0, 'code');
        $this->assertSame(2, $errorDataCollector->getErrorCount());
        $this->assertSame(1, $errorDataCollector->getFixableErrorCount());
    }

    /**
     * @expectedException \Symplify\PHP7_CodeSniffer\Exception\File\NotImplementedException
     */
    public function testNotImplementedGetErrorCount()
    {
        $this->file->getErrorCount();
    }

    /**
     * @expectedException \Symplify\PHP7_CodeSniffer\Exception\File\NotImplementedException
     */
    public function testNotImplementedGetErrors()
    {
        $this->file->getErrors();
    }

    /**
     * @expectedException \Symplify\PHP7_CodeSniffer\Exception\File\NotImplementedException
     */
    public function testNotImplementedProcess()
    {
        $this->file->process();
    }

    /**
     * @expectedException \Symplify\PHP7_CodeSniffer\Exception\File\NotImplementedException
     */
    public function testNotImplementedParse()
    {
        $this->file->parse();
    }
}
