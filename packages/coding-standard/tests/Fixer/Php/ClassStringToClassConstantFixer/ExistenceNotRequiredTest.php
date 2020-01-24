<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Php\ClassStringToClassConstantFixer;

use Iterator;
use Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class ExistenceNotRequiredTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideDataForTest()
     */
    public function test(string $file): void
    {
        $this->doTestFiles([$file]);
    }

    public function provideDataForTest(): Iterator
    {
        yield [__DIR__ . '/Fixture/wrong4.php.inc'];
        yield [__DIR__ . '/Fixture/wrong5.php.inc'];
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
        return ['class_must_exist' => false];
    }
}
