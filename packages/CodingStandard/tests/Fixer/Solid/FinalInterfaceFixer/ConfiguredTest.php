<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Solid\FinalInterfaceFixer;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Fixer\Solid\FinalInterfaceFixer
 */
final class ConfiguredTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([__DIR__ . '/Fixture/wrong3.php.inc']);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config-configured.yml';
    }
}
