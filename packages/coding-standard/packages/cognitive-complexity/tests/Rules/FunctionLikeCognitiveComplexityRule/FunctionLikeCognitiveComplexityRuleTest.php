<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\CognitiveComplexity\Tests\Rules\FunctionLikeCognitiveComplexityRule;

use Iterator;
use Nette\Configurator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\CognitiveComplexity\Rules\FunctionLikeCognitiveComplexityRule;
use Symplify\CodingStandard\CognitiveComplexity\Tests\Rules\FunctionLikeCognitiveComplexityRule\Source\ClassMethodOverComplicated;

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
        $errorMessage = sprintf(FunctionLikeCognitiveComplexityRule::ERROR_MESSAGE, 'someFunction()', 9, 8);
        yield [__DIR__ . '/Source/function.php.inc', [[$errorMessage, 3]]];

        $errorMessage = sprintf(
            FunctionLikeCognitiveComplexityRule::ERROR_MESSAGE,
            ClassMethodOverComplicated::class . '::someMethod()',
            9,
            8
        );
        yield [__DIR__ . '/Source/ClassMethodOverComplicated.php', [[$errorMessage, 7]]];
    }

    protected function getRule(): Rule
    {
        // @todo use generic factory with unique container per run config
        $configurator = new Configurator();
        $configurator->addConfig(__DIR__ . '/../../../../../config/symplify-rules.neon');
        $configurator->setTempDirectory(sys_get_temp_dir() . '/symplify_cognitive_complexity_tests');
        $container = $configurator->createContainer();

        return $container->getByType(FunctionLikeCognitiveComplexityRule::class);
    }
}
