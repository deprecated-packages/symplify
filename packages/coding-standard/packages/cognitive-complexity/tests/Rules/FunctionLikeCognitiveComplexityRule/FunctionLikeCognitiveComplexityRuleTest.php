<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\CognitiveComplexity\Tests\Rules\FunctionLikeCognitiveComplexityRule;

use Iterator;
use Nette\Configurator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\CognitiveComplexity\Rules\FunctionLikeCognitiveComplexityRule;

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
        $configurator = new Configurator();
        $configurator->addConfig(__DIR__ . '/../../../../../config/symplify-rules.neon');
        $configurator->setTempDirectory(sys_get_temp_dir() . '/symplify_cognitive_complexity_tests');
        $container = $configurator->createContainer();

        return $container->getByType(FunctionLikeCognitiveComplexityRule::class);
    }
}
