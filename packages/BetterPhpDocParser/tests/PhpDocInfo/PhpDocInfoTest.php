<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Tests\PhpDocInfo;

use Nette\Utils\FileSystem;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use Symplify\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Symplify\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Symplify\BetterPhpDocParser\Printer\PhpDocInfoPrinter;
use Symplify\BetterPhpDocParser\Tests\AbstractContainerAwareTestCase;

final class PhpDocInfoTest extends AbstractContainerAwareTestCase
{
    /**
     * @var PhpDocInfo
     */
    private $phpDocInfo;

    /**
     * @var PhpDocInfoPrinter
     */
    private $phpDocInfoPrinter;

    /**
     * @var PhpDocInfoFactory
     */
    private $phpDocInfoFactory;

    protected function setUp(): void
    {
        $phpDocInfoFactory = $this->container->get(PhpDocInfoFactory::class);

        $this->phpDocInfo = $phpDocInfoFactory->createFrom(FileSystem::read(__DIR__ . '/PhpDocInfoSource/doc.txt'));

        $this->phpDocInfoFactory = $phpDocInfoFactory;

        $this->phpDocInfoPrinter = $this->container->get(PhpDocInfoPrinter::class);
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

    public function testGetParamTypeNode(): void
    {
        $typeNode = $this->phpDocInfo->getParamTypeNode('value');

        $this->assertInstanceOf(TypeNode::class, $typeNode);
    }

    public function testGetVarTypeNode(): void
    {
        $typeNode = $this->phpDocInfo->getVarTypeNode();

        $this->assertInstanceOf(TypeNode::class, $typeNode);
    }

    public function testGetVarTypes(): void
    {
        $varTypes = $this->phpDocInfo->getVarTypes();

        $this->assertSame(['SomeType'], $varTypes);
    }

    public function testReplaceTagByAnother(): void
    {
        $phpDocInfo = $this->phpDocInfoFactory->createFrom(
            FileSystem::read(__DIR__ . '/PhpDocInfoSource/test-tag.txt')
        );

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
        /** @var IdentifierTypeNode $varTypeNode */
        $varTypeNode = $this->phpDocInfo->getVarTypeNode();
        $this->assertSame('SomeType', $varTypeNode->name);

        $this->phpDocInfo->replacePhpDocTypeByAnother('SomeType', 'AnotherType');

        /** @var IdentifierTypeNode $varTypeNode */
        $varTypeNode = $this->phpDocInfo->getVarTypeNode();
        $this->assertSame('AnotherType', $varTypeNode->name);

        $this->assertStringEqualsFile(
            __DIR__ . '/PhpDocInfoSource/expected-with-replaced-type.txt',
            $this->phpDocInfoPrinter->printFormatPreserving($this->phpDocInfo)
        );
    }
}
