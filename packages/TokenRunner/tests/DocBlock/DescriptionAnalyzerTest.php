<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Tests\DocBlock;

use Iterator;
use Symplify\BetterPhpDocParser\Attributes\Ast\PhpDoc\Type\AttributeAwareIdentifierTypeNode;
use Symplify\BetterPhpDocParser\Attributes\Attribute\Attribute;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\TokenRunner\DocBlock\DescriptionAnalyzer;
use Symplify\TokenRunner\Tests\HttpKernel\TokenRunnerKernel;

final class DescriptionAnalyzerTest extends AbstractKernelTestCase
{
    /**
     * @var DescriptionAnalyzer
     */
    private $descriptionAnalyzer;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(TokenRunnerKernel::class, [__DIR__ . '/../config/config_tests.yaml']);
        $this->descriptionAnalyzer = self::$container->get(DescriptionAnalyzer::class);
    }

    /**
     * @dataProvider provideUseful()
     * @dataProvider provideNotUseful()
     */
    public function test(
        string $description,
        AttributeAwareIdentifierTypeNode $attributeAwareIdentifierTypeNode,
        string $name,
        bool $expectedIsUseful
    ): void {
        $attributeAwareIdentifierTypeNode->setAttribute(
            Attribute::TYPE_AS_ARRAY,
            [$attributeAwareIdentifierTypeNode->name]
        );
        $attributeAwareIdentifierTypeNode->setAttribute(
            Attribute::TYPE_AS_STRING,
            $attributeAwareIdentifierTypeNode->name
        );

        $isUseful = $this->descriptionAnalyzer->isDescriptionUseful(
            $description,
            $attributeAwareIdentifierTypeNode,
            $name
        );

        $this->assertSame($expectedIsUseful, $isUseful);
    }

    public function provideUseful(): Iterator
    {
        yield ['this is description', new AttributeAwareIdentifierTypeNode('type'), 'name', true];
        yield ['column list', new AttributeAwareIdentifierTypeNode('string'), 'columnsList', true];
    }

    public function provideNotUseful(): Iterator
    {
        yield ['current table', new AttributeAwareIdentifierTypeNode('string'), 'table', false];
        yield ['columns list', new AttributeAwareIdentifierTypeNode('string'), 'columnsList', false];
        yield ['Form name', new AttributeAwareIdentifierTypeNode('string'), 'formName', false];
        yield ['rule itself', new AttributeAwareIdentifierTypeNode('string'), 'rule', false];
        yield ['rule                  itself', new AttributeAwareIdentifierTypeNode('string'), 'rule', false];

        yield ['a Type instance', new AttributeAwareIdentifierTypeNode('Type'), 'name', false];
        yield ['an Type instance', new AttributeAwareIdentifierTypeNode('Type'), 'name', false];
        yield ['an     Type      instance', new AttributeAwareIdentifierTypeNode('Type'), 'name', false];
        yield ['an \Type instance', new AttributeAwareIdentifierTypeNode('Type'), 'name', false];
        yield ['an    \Type instance', new AttributeAwareIdentifierTypeNode('Type'), 'name', false];
        yield ['an TypeInterface instance', new AttributeAwareIdentifierTypeNode('Type'), 'name', false];
        yield ['the TypeInterface instance', new AttributeAwareIdentifierTypeNode('Type'), 'name', false];
        yield ['the \TypeInterface instance', new AttributeAwareIdentifierTypeNode('Type'), 'name', false];
        yield [
            'the \Namespaced\TypeInterface instance',
            new AttributeAwareIdentifierTypeNode('Namespaced\TypeInterface'),
            'name',
            false,
        ];
        yield [
            'a \Namespaced\TypeInterface',
            new AttributeAwareIdentifierTypeNode('Namespaced\TypeInterface'),
            'name',
            false,
        ];
        yield [
            '\Namespaced\TypeInterface',
            new AttributeAwareIdentifierTypeNode('Namespaced\TypeInterface'),
            'name',
            false,
        ];
        yield ['name', new AttributeAwareIdentifierTypeNode('Namespaced\TypeInterface'), 'name', false];
        yield ['a name', new AttributeAwareIdentifierTypeNode('Namespaced\TypeInterface'), 'name', false];
    }
}
