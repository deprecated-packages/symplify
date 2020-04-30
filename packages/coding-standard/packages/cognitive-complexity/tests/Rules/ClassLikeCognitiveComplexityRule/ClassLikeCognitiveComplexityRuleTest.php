<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\CognitiveComplexity\Tests\Rules\ClassLikeCognitiveComplexityRule;

use Iterator;
use Nette\Configurator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\CognitiveComplexity\Rules\ClassLikeCognitiveComplexityRule;

final class ClassLikeCognitiveComplexityRuleTest extends RuleTestCase
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
            __DIR__ . '/Source/ClassWithManyComplexMethods.php',
            [['Cognitive complexity for "ClassWithManyComplexMethods" class is 54, keep it under 50', 7]],
        ];
    }

    protected function getRule(): Rule
    {
        // @todo decouple to AbstractPHPStanContainerTestCase
        $configurator = new Configurator();
        $configurator->addConfig(__DIR__ . '/../../../../../config/symplify-rules.neon');
        $configurator->setTempDirectory(sys_get_temp_dir() . '/symplify_cognitive_complexity_tests_2');
        $container = $configurator->createContainer();

        return $container->getByType(ClassLikeCognitiveComplexityRule::class);
    }
}
