<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Php\ClassStringToClassConstantFixer;

use DateTimeInterface;
use Iterator;
use Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class AllowedClassesTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideDataForTestWrongToFixed()
     */
    public function testWrongToFixed(string $file): void
    {
        $this->doTestFiles([$file]);
    }

    public function provideDataForTestWrongToFixed(): Iterator
    {
        yield [__DIR__ . '/Fixture/correct2.php.inc'];
    }

    protected function getCheckerClass(): string
    {
        return ClassStringToClassConstantFixer::class;
    }

    /**
     * @return mixed[]
     */
    protected function getCheckerConfiguration(): array
    {
        return [
            'allow_classes' => [DateTimeInterface::class],
        ];
    }
}
