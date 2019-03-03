<?php declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Tests\DocBlock;

use Iterator;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\TokenRunner\DocBlock\ParamAndReturnTagAnalyzer;

final class ParamAndReturnTagAnalyzerTest extends TestCase
{
    /**
     * @var ParamAndReturnTagAnalyzer
     */
    private $paramAndReturnTagAnalyzer;

    protected function setUp(): void
    {
        $this->paramAndReturnTagAnalyzer = new ParamAndReturnTagAnalyzer();
    }

    /**
     * @dataProvider provideDocTypeDocDescriptionParamTypeAndResult()
     * @param string[] $paramTypes
     */
    public function test(TypeNode $typeNode, ?string $docDescription, array $paramTypes, bool $expectedIsUseful): void
    {
        $isUseful = $this->paramAndReturnTagAnalyzer->isTagUseful($typeNode, $docDescription, $paramTypes);

        $this->assertSame($expectedIsUseful, $isUseful);
    }

    public function provideDocTypeDocDescriptionParamTypeAndResult(): Iterator
    {
        # useful
        yield [new IdentifierTypeNode('boolean'), 'some description', ['bool'], true];
        # not useful
        yield [new IdentifierTypeNode('boolean'), null, ['bool'], false];
        yield [new IdentifierTypeNode('integer'), null, ['int'], false];
    }
}
