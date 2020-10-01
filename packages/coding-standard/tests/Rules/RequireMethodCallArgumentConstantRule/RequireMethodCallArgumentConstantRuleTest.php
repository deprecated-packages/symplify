<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\RequireMethodCallArgumentConstantRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symfony\Component\DependencyInjection\Loader\Configurator\ParametersConfigurator;
use Symplify\CodingStandard\Rules\RequireMethodCallArgumentConstantRule;
use Symplify\CodingStandard\Tests\Rules\RequireMethodCallArgumentConstantRule\Source\AlwaysCallMeWithConstant;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class RequireMethodCallArgumentConstantRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        $errorMessage = sprintf(RequireMethodCallArgumentConstantRule::ERROR_MESSAGE, 0);
        yield [__DIR__ . '/Fixture/SomeMethodCallWithoutConstant.php', [[$errorMessage, 14]]];
        yield [__DIR__ . '/Fixture/SymfonyPHPConfigParameterSetter.php', [[$errorMessage, 14]]];

        yield [__DIR__ . '/Fixture/WithConstant.php', []];
        yield [__DIR__ . '/Fixture/SkipWithVariable.php', []];
    }

    protected function getRule(): Rule
    {
        return new RequireMethodCallArgumentConstantRule([
            AlwaysCallMeWithConstant::class => [
                'call' => [0],
            ],
            ParametersConfigurator::class => [
                'set' => [0],
            ],
        ]);
    }
}
