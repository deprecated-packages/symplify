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
    public function testPrintFormatPreserving(string $docComment): void
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFrom($docComment);

        $this->assertSame($docComment, $this->phpDocInfoPrinter->printFormatPreserving($phpDocInfo));
    }

    public function provideDocFilesForPrint(): Iterator
    {
        yield ['/** @var Type */'];
        yield ['/**  @var Type */'];
        yield ['/**  @var Type  */'];
        yield [file_get_contents(__DIR__ . '/PhpDocInfoPrinterSource/doc.txt')];
        yield [file_get_contents(__DIR__ . '/PhpDocInfoPrinterSource/doc2.txt')];
        yield [file_get_contents(__DIR__ . '/PhpDocInfoPrinterSource/doc3.txt')];
        yield [file_get_contents(__DIR__ . '/PhpDocInfoPrinterSource/doc4.txt')];
        yield [file_get_contents(__DIR__ . '/PhpDocInfoPrinterSource/doc5.txt')];
    }

    /**
     * @dataProvider provideDocFilesToEmpty()
     */
    public function testPrintFormatPreservingEmpty(string $docComment): void
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFrom($docComment);

        $this->assertSame('', $this->phpDocInfoPrinter->printFormatPreserving($phpDocInfo));
    }

    public function provideDocFilesToEmpty(): Iterator
    {
        yield [file_get_contents(__DIR__ . '/PhpDocInfoPrinterSource/empty-doc.txt')];
    }
}
