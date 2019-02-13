<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Tests\PhpDocInfo;

use Nette\Utils\FileSystem;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use Symplify\BetterPhpDocParser\HttpKernel\BetterPhpDocParserKernel;
use Symplify\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Symplify\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Symplify\BetterPhpDocParser\Printer\PhpDocInfoPrinter;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class PhpDocInfoTest extends AbstractKernelTestCase
{
    /**
     * @var PhpDocInfo
     */
    private $phpDocInfo;

    /**
     * @var PhpDocInfoPrinter
     */
    private $phpDocInfoPrinter;

    protected function setUp(): void
    {
        $this->bootKernel(BetterPhpDocParserKernel::class);

        $this->phpDocInfo = $this->createPhpDocInfoFromFile(__DIR__ . '/PhpDocInfoSource/doc.txt');
        $this->phpDocInfoPrinter = self::$container->get(PhpDocInfoPrinter::class);
    }

    public function testHasTag(): void
    {
        $this->assertTrue($this->phpDocInfo->hasTag('param'));
        $this->assertTrue($this->phpDocInfo->hasTag('@throw'));

        $this->assertFalse($this->phpDocInfo->hasTag('random'));
    }

    public function testGetTagsByName(): void
    {
        $paramTags = $this->phpDocInfo->getTagsByName('param');
        $this->assertCount(2, $paramTags);
    }

    public function testParamTypeNode(): void
    {
        $typeNode = $this->phpDocInfo->getParamTypeNode('value');
        $this->assertInstanceOf(TypeNode::class, $typeNode);

        $this->assertSame(
            ['SomeType', 'NoSlash', '\Preslashed', 'null', '\string'],
            $this->phpDocInfo->getParamTypes('value')
        );
    }

    public function testGetVarTypes(): void
    {
        $this->assertSame(['SomeType'], $this->phpDocInfo->getVarTypes());
    }

    public function testReturn(): void
    {
        $this->assertSame(['SomeType'], $this->phpDocInfo->getReturnTypes());
    }

    public function testReplaceTagByAnother(): void
    {
        $phpDocInfo = $this->createPhpDocInfoFromFile(__DIR__ . '/PhpDocInfoSource/test-tag.txt');

        $this->assertFalse($phpDocInfo->hasTag('flow'));
        $this->assertTrue($phpDocInfo->hasTag('test'));

        $phpDocInfo->replaceTagByAnother('test', 'flow');

        $this->assertFalse($phpDocInfo->hasTag('test'));
        $this->assertTrue($phpDocInfo->hasTag('flow'));

        $this->assertStringEqualsFile(
            __DIR__ . '/PhpDocInfoSource/expected-replaced-tag.txt',
            $this->phpDocInfoPrinter->printFormatPreserving($phpDocInfo)
        );
    }

    public function testReplacePhpDocTypeByAnother(): void
    {
        $this->assertSame(['SomeType'], $this->phpDocInfo->getVarTypes());
        $this->phpDocInfo->replacePhpDocTypeByAnother('SomeType', 'AnotherType');

        $this->assertSame(['AnotherType'], $this->phpDocInfo->getVarTypes());

        $this->assertStringEqualsFile(
            __DIR__ . '/PhpDocInfoSource/expected-with-replaced-type.txt',
            $this->phpDocInfoPrinter->printFormatPreserving($this->phpDocInfo)
        );
    }

    private function createPhpDocInfoFromFile(string $path): PhpDocInfo
    {
        $phpDocInfoFactory = self::$container->get(PhpDocInfoFactory::class);
        $phpDocContent = FileSystem::read($path);

        return $phpDocInfoFactory->createFrom($phpDocContent);
    }
}
