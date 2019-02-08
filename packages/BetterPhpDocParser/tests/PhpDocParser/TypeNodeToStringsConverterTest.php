<?php declare(strict_types=1);

namespace Symplify\BetterPhpDocParser\Tests\PhpDocParser;

use Iterator;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use Symplify\BetterPhpDocParser\HttpKernel\BetterPhpDocParserKernel;
use Symplify\BetterPhpDocParser\PhpDocParser\TypeNodeToStringsConverter;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class TypeNodeToStringsConverterTest extends AbstractKernelTestCase
{
    /**
     * @var TypeNodeToStringsConverter
     */
    private $typeNodeToStringsConverter;

    protected function setUp(): void
    {
        $this->bootKernel(BetterPhpDocParserKernel::class);

        $this->typeNodeToStringsConverter = self::$container->get(TypeNodeToStringsConverter::class);
    }

    /**
     * @dataProvider provideDataForConvert()
     * @param string[] $expectedTypeStrings
     */
    public function testConvert(TypeNode $typeNode, array $expectedTypeStrings): void
    {
        $this->assertSame($expectedTypeStrings, $this->typeNodeToStringsConverter->convert($typeNode));
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
