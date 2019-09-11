<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Php\ClassStringToClassConstantFixer;

use DateTimeInterface;
use Symplify\CodingStandard\Fixer\Php\ClassStringToClassConstantFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class AllowedClassesTest extends AbstractCheckerTestCase
{
    public function testWrongToFixed(): void
    {
        $this->doTestFiles([__DIR__ . '/Fixture/correct2.php.inc']);
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
