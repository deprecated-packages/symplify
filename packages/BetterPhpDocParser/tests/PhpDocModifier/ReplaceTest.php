<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Tests\PhpDocModifier;

use Iterator;
use Nette\Utils\FileSystem;
use Symplify\BetterPhpDocParser\HttpKernel\BetterPhpDocParserKernel;
use Symplify\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Symplify\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Symplify\BetterPhpDocParser\PhpDocModifier;
use Symplify\BetterPhpDocParser\Printer\PhpDocInfoPrinter;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class ReplaceTest extends AbstractKernelTestCase
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
        $this->bootKernel(BetterPhpDocParserKernel::class);

        $this->phpDocInfoFactory = self::$container->get(PhpDocInfoFactory::class);
        $this->phpDocInfoPrinter = self::$container->get(PhpDocInfoPrinter::class);
        $this->phpDocModifier = self::$container->get(PhpDocModifier::class);
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
        yield [
            __DIR__ . '/ReplaceSource/before2.txt',
            'PHP_Filter',
            'PHP\Filter',
            __DIR__ . '/ReplaceSource/after2.txt',
        ];
    }

    private function createPhpDocInfoFromFile(string $originalFile): PhpDocInfo
    {
        return $this->phpDocInfoFactory->createFrom(FileSystem::read($originalFile));
    }
}
