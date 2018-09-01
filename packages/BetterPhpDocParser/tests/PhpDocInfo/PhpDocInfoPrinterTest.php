<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Tests\PhpDocInfo;

use Iterator;
use Nette\Utils\FileSystem;
use Symplify\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Symplify\BetterPhpDocParser\Printer\PhpDocInfoPrinter;
use Symplify\BetterPhpDocParser\Tests\AbstractContainerAwareTestCase;

final class PhpDocInfoPrinterTest extends AbstractContainerAwareTestCase
{
    /**
     * @var PhpDocInfoFactory
     */
    private $phpDocInfoFactory;

    /**
     * @var PhpDocInfoPrinter
     */
    private $phpDocInfoPrinter;

    protected function setUp(): void
    {
        $this->phpDocInfoFactory = $this->container->get(PhpDocInfoFactory::class);
        $this->phpDocInfoPrinter = $this->container->get(PhpDocInfoPrinter::class);
    }

    /**
     * @dataProvider provideDocFilesForPrint()
     */
    public function testPrintFormatPreserving(string $docFilePath): void
    {
        $docComment = FileSystem::read($docFilePath);
        $phpDocInfo = $this->phpDocInfoFactory->createFrom($docComment);

        $this->assertSame($docComment, $this->phpDocInfoPrinter->printFormatPreserving($phpDocInfo));
    }

    public function provideDocFilesForPrint(): Iterator
    {
        yield [__DIR__ . '/PhpDocInfoPrinterSource/doc.txt'];
        yield [__DIR__ . '/PhpDocInfoPrinterSource/doc2.txt'];
        yield [__DIR__ . '/PhpDocInfoPrinterSource/doc3.txt'];
        yield [__DIR__ . '/PhpDocInfoPrinterSource/doc4.txt'];
        yield [__DIR__ . '/PhpDocInfoPrinterSource/doc5.txt'];
        yield [__DIR__ . '/PhpDocInfoPrinterSource/doc6.txt'];
        yield [__DIR__ . '/PhpDocInfoPrinterSource/doc7.txt'];
        yield [__DIR__ . '/PhpDocInfoPrinterSource/doc8.txt'];
        yield [__DIR__ . '/PhpDocInfoPrinterSource/doc9.txt'];
        yield [__DIR__ . '/PhpDocInfoPrinterSource/doc10.txt'];
        yield [__DIR__ . '/PhpDocInfoPrinterSource/doc11.txt'];
        yield [__DIR__ . '/PhpDocInfoPrinterSource/doc12.txt'];
        yield [__DIR__ . '/PhpDocInfoPrinterSource/doc13.txt'];
    }

    /**
     * @dataProvider provideDocFilesToEmpty()
     */
    public function testPrintFormatPreservingEmpty(string $docFilePath): void
    {
        $docComment = FileSystem::read($docFilePath);
        $phpDocInfo = $this->phpDocInfoFactory->createFrom($docComment);

        $this->assertEmpty($this->phpDocInfoPrinter->printFormatPreserving($phpDocInfo));
    }

    public function provideDocFilesToEmpty(): Iterator
    {
        yield [__DIR__ . '/PhpDocInfoPrinterSource/empty-doc.txt'];
    }
}
