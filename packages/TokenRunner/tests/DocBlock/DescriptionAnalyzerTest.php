<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Tests\DocBlock;

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
        $isUseful = $this->descriptionAnalyzer->isDescriptionUseful(
            $description,
            $type,
            $name
        );

        $this->assertSame($expectedIsUseful, $isUseful);
    }

    /**
     * @return string[][]|bool[][]
     */
    public function provideDescriptionTypeNameAndResult(): array
    {
        return [
            # useful
            ['this is description', 'type', 'name', true],
            # not useful
            ['a Type instance', 'Type', 'name', false],
            ['an Type instance', 'Type', 'name', false],
            ['an \Type instance', 'Type', 'name', false],
            ['an TypeInterface instance', 'Type', 'name', false],
            ['the TypeInterface instance', 'Type', 'name', false],
            ['the \TypeInterface instance', 'Type', 'name', false],
            ['the \Namespaced\TypeInterface instance', 'Namespaced\TypeInterface', 'name', false],
            ['a \Namespaced\TypeInterface', 'Namespaced\TypeInterface', 'name', false],
            ['\Namespaced\TypeInterface', 'Namespaced\TypeInterface', 'name', false],
            ['name', 'Namespaced\TypeInterface', 'name', false],
            ['a name', 'Namespaced\TypeInterface', 'name', false],
        ];
    }
}
