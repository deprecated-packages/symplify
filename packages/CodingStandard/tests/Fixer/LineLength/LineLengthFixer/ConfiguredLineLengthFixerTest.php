<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\LineLength\LineLengthFixer;

use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class ConfiguredLineLengthFixerTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([__DIR__ . '/Fixture/configured-wrong.php.inc']);
    }

    protected function getCheckerClass(): string
    {
        return LineLengthFixer::class;
    }

    /**
     * @return mixed[]
     */
    protected function getCheckerConfiguration(): array
    {
        return [
            'line_length' => 100,
            'break_long_lines' => true,
            'inline_short_lines' => false,
        ];
    }
}
