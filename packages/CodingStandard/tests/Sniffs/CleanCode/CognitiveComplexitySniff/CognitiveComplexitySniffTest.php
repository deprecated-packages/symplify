<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\CleanCode\CognitiveComplexitySniff;

use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;

/**
 * @see \Symplify\CodingStandard\Sniffs\CleanCode\CognitiveComplexitySniff
 */
final class CognitiveComplexitySniffTest extends AbstractCheckerTestCase
{
    public function test(): void
    {
        $this->doTestFiles([__DIR__ . '/Fixture/wrong.php.inc']); // #9
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config.yml';
    }
}
