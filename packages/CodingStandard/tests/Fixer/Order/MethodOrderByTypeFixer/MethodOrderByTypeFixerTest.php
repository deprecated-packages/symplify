<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Order\MethodOrderByTypeFixer;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Fixer\Order\MethodOrderByTypeFixer
 */
final class MethodOrderByTypeFixerTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->autoloadTestFixture = true;

        $this->doTestFiles([
            __DIR__ . '/Fixture/AbstractClass.php.inc',
            __DIR__ . '/Fixture/FixerWithAbstractParent.php',
            __DIR__ . '/Fixture/SomeFixer.php.inc',
            __DIR__ . '/Fixture/RealFixer.php.inc',
        ]);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
