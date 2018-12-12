<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveUselessDocBlockFixer;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDocBlockFixer
 */
final class ConfiguredTest extends AbstractCheckerTestCase
{
    public function testFix(): void
    {
        $this->doTestFiles([__DIR__ . '/Fixture/wrong13.php.inc', __DIR__ . '/Fixture/wrong14.php.inc']);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config-configured.yml';
    }
}
