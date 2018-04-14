<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Tests\DocBlock;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\TokenRunner\DocBlock\DescriptionAnalyzer;

final class DescriptionAnalyzerTest extends TestCase
{
    /**
     * @var DescriptionAnalyzer
     */
    private $descriptionAnalyzer;

    protected function setUp(): void
    {
        $this->descriptionAnalyzer = new DescriptionAnalyzer();
    }

    /**
     * @dataProvider provideDescriptionTypeNameAndResult()
     */
    public function test(string $description, string $type, string $name, bool $expectedIsUseful): void
    {
        $isUseful = $this->descriptionAnalyzer->isDescriptionUseful($description, $type, $name);

        $this->assertSame($expectedIsUseful, $isUseful);
    }

    public function provideDescriptionTypeNameAndResult(): Iterator
    {
        # useful
        yield ['this is description', 'type', 'name', true];
        # not useful
        yield ['a Type instance', 'Type', 'name', false];
        yield ['an Type instance', 'Type', 'name', false];
        yield ['an \Type instance', 'Type', 'name', false];
        yield ['an TypeInterface instance', 'Type', 'name', false];
        yield ['the TypeInterface instance', 'Type', 'name', false];
        yield ['the \TypeInterface instance', 'Type', 'name', false];
        yield ['the \Namespaced\TypeInterface instance', 'Namespaced\TypeInterface', 'name', false];
        yield ['a \Namespaced\TypeInterface', 'Namespaced\TypeInterface', 'name', false];
        yield ['\Namespaced\TypeInterface', 'Namespaced\TypeInterface', 'name', false];
        yield ['name', 'Namespaced\TypeInterface', 'name', false];
        yield ['a name', 'Namespaced\TypeInterface', 'name', false];
    }
}
