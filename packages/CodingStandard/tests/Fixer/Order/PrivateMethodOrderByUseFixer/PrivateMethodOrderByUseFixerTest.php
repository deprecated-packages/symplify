<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Order\PrivateMethodOrderByUseFixer;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Fixer\Order\PrivateMethodOrderByUseFixer
 */
final class PrivateMethodOrderByUseFixerTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([__DIR__ . '/Fixture/wrong.php.inc', __DIR__ . '/Fixture/wrong2.php.inc']);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
