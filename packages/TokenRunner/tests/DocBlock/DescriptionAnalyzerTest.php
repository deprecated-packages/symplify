<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Tests\DocBlock;

use Iterator;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPUnit\Framework\TestCase;
use Symplify\BetterPhpDocParser\PhpDocParser\TypeNodeAnalyzer;
use Symplify\BetterPhpDocParser\PhpDocParser\TypeNodeToStringsConverter;
use Symplify\TokenRunner\DocBlock\DescriptionAnalyzer;

final class DescriptionAnalyzerTest extends TestCase
{
    /**
     * @var DescriptionAnalyzer
     */
    private $descriptionAnalyzer;

    protected function setUp(): void
    {
        $this->descriptionAnalyzer = new DescriptionAnalyzer(new TypeNodeAnalyzer(), new TypeNodeToStringsConverter());
    }

    /**
     * @dataProvider provideUseful()
     * @dataProvider provideNotUseful()
     */
    public function test(string $description, TypeNode $typeNode, string $name, bool $expectedIsUseful): void
    {
        $isUseful = $this->descriptionAnalyzer->isDescriptionUseful($description, $typeNode, $name);

        $this->assertSame($expectedIsUseful, $isUseful);
    }

    public function provideUseful(): Iterator
    {
        yield ['this is description', new IdentifierTypeNode('type'), 'name', true];
        yield ['column list', new IdentifierTypeNode('string'), 'columnsList', true];
    }

    public function provideNotUseful(): Iterator
    {
        yield ['current table', new IdentifierTypeNode('string'), 'table', false];
        yield ['columns list', new IdentifierTypeNode('string'), 'columnsList', false];
        yield ['Form name', new IdentifierTypeNode('string'), 'formName', false];
        yield ['rule itself', new IdentifierTypeNode('string'), 'rule', false];
        yield ['rule                  itself', new IdentifierTypeNode('string'), 'rule', false];

        yield ['a Type instance', new IdentifierTypeNode('Type'), 'name', false];
        yield ['an Type instance', new IdentifierTypeNode('Type'), 'name', false];
        yield ['an     Type      instance', new IdentifierTypeNode('Type'), 'name', false];
        yield ['an \Type instance', new IdentifierTypeNode('Type'), 'name', false];
        yield ['an    \Type instance', new IdentifierTypeNode('Type'), 'name', false];
        yield ['an TypeInterface instance', new IdentifierTypeNode('Type'), 'name', false];
        yield ['the TypeInterface instance', new IdentifierTypeNode('Type'), 'name', false];
        yield ['the \TypeInterface instance', new IdentifierTypeNode('Type'), 'name', false];
        yield [
            'the \Namespaced\TypeInterface instance',
            new IdentifierTypeNode('Namespaced\TypeInterface'),
            'name',
            false,
        ];
        yield ['a \Namespaced\TypeInterface', new IdentifierTypeNode('Namespaced\TypeInterface'), 'name', false];
        yield ['\Namespaced\TypeInterface', new IdentifierTypeNode('Namespaced\TypeInterface'), 'name', false];
        yield ['name', new IdentifierTypeNode('Namespaced\TypeInterface'), 'name', false];
        yield ['a name', new IdentifierTypeNode('Namespaced\TypeInterface'), 'name', false];
    }
}
