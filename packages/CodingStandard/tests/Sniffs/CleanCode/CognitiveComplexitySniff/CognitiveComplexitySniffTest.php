<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\CleanCode\CognitiveComplexitySniff;

use Symplify\CodingStandard\Sniffs\CleanCode\CognitiveComplexitySniff;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

final class CognitiveComplexitySniffTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([__DIR__ . '/Fixture/wrong.php.inc']); // #9
    }

    protected function getCheckerClass(): string
    {
        return CognitiveComplexitySniff::class;
    }
}
