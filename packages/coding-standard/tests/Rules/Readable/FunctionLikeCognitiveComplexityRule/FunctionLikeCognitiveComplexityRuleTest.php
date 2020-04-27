<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\Readable\FunctionLikeCognitiveComplexityRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\Readable\FunctionLikeCognitiveComplexityRule;
use Symplify\CodingStandard\Tests\HttpKernel\SymplifyCodingStandardKernel;

final class FunctionLikeCognitiveComplexityRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideDataForTest()
     */
    public function test(string $file, ?array $expectedMessagesWithLines = null): void
    {
        $this->analyse([$file], $expectedMessagesWithLines);
    }

    public function provideDataForTest(): Iterator
    {
        yield [
            __DIR__ . '/Source/function.php.inc',
            [['Cognitive complexity for "someFunction()" is 9, keep it under 8', 3]],
        ];
    }

    protected function getRule(): Rule
    {
        $symplifyCodingStandardKernel = new SymplifyCodingStandardKernel('prod', true);
        $symplifyCodingStandardKernel->boot();
        $container = $symplifyCodingStandardKernel->getContainer();

        return $container->get(FunctionLikeCognitiveComplexityRule::class);
    }
}
