<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Tests\PhpDocParser;

use Iterator;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use Symplify\BetterPhpDocParser\PhpDocParser\TypeNodeToStringsConvertor;
use Symplify\BetterPhpDocParser\Tests\AbstractContainerAwareTestCase;

final class TypeNodeToStringsConvertorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var TypeNodeToStringsConvertor
     */
    private $typeNodeToStringsConvertor;

    protected function setUp(): void
    {
        $this->typeNodeToStringsConvertor = $this->container->get(TypeNodeToStringsConvertor::class);
    }

    /**
     * @dataProvider provideDataForConvert()
     * @param string[] $expectedTypeStrings
     */
    public function testConvert(TypeNode $typeNode, array $expectedTypeStrings): void
    {
        $this->assertSame($expectedTypeStrings, $this->typeNodeToStringsConvertor->convert($typeNode));
    }

    public function provideDataForConvert(): Iterator
    {
        $identifierTypeNode = new IdentifierTypeNode('int');
        $anotherIdentifierTypeNode = new IdentifierTypeNode('string');

        yield [$identifierTypeNode, ['int']];
        yield [new UnionTypeNode([$identifierTypeNode, $anotherIdentifierTypeNode]), ['int', 'string']];
        yield [new IntersectionTypeNode([$identifierTypeNode, $anotherIdentifierTypeNode]), ['int&string']];

        $arrayTypeNode = new ArrayTypeNode($anotherIdentifierTypeNode);
        yield [new UnionTypeNode([$identifierTypeNode, $arrayTypeNode]), ['int', 'string[]']];
        yield [new IntersectionTypeNode([$identifierTypeNode, $arrayTypeNode]), ['int&string[]']];
    }
}
