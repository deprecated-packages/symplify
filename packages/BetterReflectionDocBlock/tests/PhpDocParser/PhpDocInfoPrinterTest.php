<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\Tests\PhpDocParser;

use Iterator;
use Symplify\BetterReflectionDocBlock\PhpDocParser\PhpDocInfoFactory;
use Symplify\BetterReflectionDocBlock\PhpDocParser\PhpDocInfoPrinter;
use Symplify\BetterReflectionDocBlock\Tests\AbstractContainerAwareTestCase;

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
    public function testSingleLine(string $docFile): void
    {
        $docComment = file_get_contents($docFile);

        $phpDocInfo = $this->phpDocInfoFactory->createFrom($docComment);
        $this->assertSame($docComment, $this->phpDocInfoPrinter->print($phpDocInfo));
    }

    public function provideDocFilesForPrint(): Iterator
    {
        yield [__DIR__ . '/PhpDocInfoPrinterSource/doc.txt'];
        yield [__DIR__ . '/PhpDocInfoPrinterSource/doc2.txt'];
    }

    /**
     * @dataProvider provideDocFilesForPrintFormatPreserving()
     */
    public function testPrintFormatPreserving(string $docFile): void
    {
        $docComment = file_get_contents($docFile);

        $phpDocInfo = $this->phpDocInfoFactory->createFrom($docComment);
        $this->assertSame($docComment, $this->phpDocInfoPrinter->print($phpDocInfo));
    }

    public function provideDocFilesForPrintFormatPreserving(): Iterator
    {
        yield [__DIR__ . '/PhpDocInfoPrinterSource/doc3.txt'];
    }
}
