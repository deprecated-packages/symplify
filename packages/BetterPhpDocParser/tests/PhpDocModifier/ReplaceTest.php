<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Tests\PhpDocModifier;

use Iterator;
use Nette\Utils\FileSystem;
use Symplify\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Symplify\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Symplify\BetterPhpDocParser\PhpDocModifier;
use Symplify\BetterPhpDocParser\Printer\PhpDocInfoPrinter;
use Symplify\BetterPhpDocParser\Tests\AbstractContainerAwareTestCase;

final class ReplaceTest extends AbstractContainerAwareTestCase
{
    /**
     * @var PhpDocInfoFactory
     */
    private $phpDocInfoFactory;

    /**
     * @var PhpDocInfoPrinter
     */
    private $phpDocInfoPrinter;

    /**
     * @var PhpDocModifier
     */
    private $phpDocModifier;

    protected function setUp(): void
    {
        $this->phpDocInfoFactory = $this->container->get(PhpDocInfoFactory::class);
        $this->phpDocInfoPrinter = $this->container->get(PhpDocInfoPrinter::class);
        $this->phpDocModifier = $this->container->get(PhpDocModifier::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $originalFile, string $oldType, string $newType, string $expectedFile): void
    {
        $phpDocInfo = $this->createPhpDocInfoFromFile($originalFile);
        $this->phpDocModifier->replacePhpDocTypeByAnother($phpDocInfo->getPhpDocNode(), $oldType, $newType);

        $newPhpDocContent = $this->phpDocInfoPrinter->printFormatPreserving($phpDocInfo);
        $this->assertStringEqualsFile($expectedFile, $newPhpDocContent);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/ReplaceSource/before.txt', 'PHP_Filter', 'PHP\Filter', __DIR__ . '/ReplaceSource/after.txt'];
        yield [__DIR__ . '/ReplaceSource/before2.txt', 'PHP_Filter', 'PHP\Filter', __DIR__ . '/ReplaceSource/after2.txt'];
    }

    private function createPhpDocInfoFromFile(string $originalFile): PhpDocInfo
    {
        return $this->phpDocInfoFactory->createFrom(FileSystem::read($originalFile));
    }
}
