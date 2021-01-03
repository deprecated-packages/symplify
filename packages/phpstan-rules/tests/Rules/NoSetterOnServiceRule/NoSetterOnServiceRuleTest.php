<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoSetterOnServiceRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoSetterOnServiceRule;

final class NoSetterOnServiceRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/Service/SkipInterfaceRequired.php', []];
        yield [__DIR__ . '/Fixture/Entity/SkipSomeEntity.php', []];
        yield [__DIR__ . '/Fixture/Event/SkipSomeEvent.php', []];
        yield [__DIR__ . '/Fixture/ValueObject/SkipSomeValueObject.php', []];
        yield [__DIR__ . '/Fixture/Service/SkipSomeService.php', []];
        yield [__DIR__ . '/Fixture/Service/SkipSomeServiceWithPrivateSetter.php', []];

        yield [__DIR__ . '/Fixture/Service/SomeServiceWithSetter.php', [[NoSetterOnServiceRule::ERROR_MESSAGE, 11]]];
        yield [
            __DIR__ . '/Fixture/Service/SomeServiceWithSetterStaticProperty.php',
            [[NoSetterOnServiceRule::ERROR_MESSAGE, 11]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoSetterOnServiceRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
