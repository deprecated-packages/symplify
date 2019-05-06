<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Php\ClassStringToClassConstantFixer;

use Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class ExistenceNotRequiredTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([__DIR__ . '/Fixture/wrong4.php.inc', __DIR__ . '/Fixture/wrong5.php.inc']);
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
            'class_must_exist' => false,
        ];
    }
}
