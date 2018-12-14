<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveUselessDocBlockFixer;

use Symplify\CodingStandard\Fixer\Commenting\RemoveUselessDocBlockFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class ConfiguredTest extends AbstractCheckerTestCase
{
    public function testFix(): void
    {
        $this->doTestFiles([__DIR__ . '/Fixture/wrong13.php.inc', __DIR__ . '/Fixture/wrong14.php.inc']);
    }

    protected function getCheckerClass(): string
    {
        return RemoveUselessDocBlockFixer::class;
    }

    /**
     * @return mixed[]
     */
    protected function getCheckerConfiguration(): array
    {
        return ['useless_types' => ['object', 'mixed']];
    }
}
