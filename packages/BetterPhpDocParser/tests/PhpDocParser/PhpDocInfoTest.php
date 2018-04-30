<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Tests\PhpDocParser;

use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use Symplify\BetterPhpDocParser\PhpDocParser\PhpDocInfo;
use Symplify\BetterPhpDocParser\PhpDocParser\PhpDocInfoFactory;
use Symplify\BetterPhpDocParser\Tests\AbstractContainerAwareTestCase;

final class PhpDocInfoTest extends AbstractContainerAwareTestCase
{
    /**
     * @var PhpDocInfo
     */
    private $phpDocInfo;

    protected function setUp(): void
    {
        /** @var PhpDocInfoFactory $phpDocInfoFactory */
        $phpDocInfoFactory = $this->container->get(PhpDocInfoFactory::class);

        $this->phpDocInfo = $phpDocInfoFactory->createFrom(file_get_contents(__DIR__ . '/PhpDocInfoSource/doc.txt'));
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

    public function testReplaceTagByAnother(): void
    {
        $this->assertFalse($this->phpDocInfo->hasTag('flow'));
        $this->assertTrue($this->phpDocInfo->hasTag('throw'));

        $this->phpDocInfo->replaceTagByAnother('throw', 'flow');

        $this->assertFalse($this->phpDocInfo->hasTag('throw'));
        $this->assertTrue($this->phpDocInfo->hasTag('flow'));
    }

    public function testReplacePhpDocTypeByAnother(): void
    {
        $this->assertSame('SomeType', $this->phpDocInfo->getVarTypeNode()->name);

        $this->phpDocInfo->replacePhpDocTypeByAnother('SomeType', 'AnotherType');

        $this->assertSame('AnotherSomeType', $this->phpDocInfo->getVarTypeNode()->name);
    }
}
