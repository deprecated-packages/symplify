<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoSetterOnServiceRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\NoSetterOnServiceRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

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
        yield [__DIR__ . '/Fixture/Entity/SomeEntity.php', []];
        yield [__DIR__ . '/Fixture/Event/SomeEvent.php', []];
        yield [__DIR__ . '/Fixture/ValueObject/SomeValueObject.php', []];
        yield [__DIR__ . '/Fixture/Service/SomeService.php', []];
        yield [__DIR__ . '/Fixture/Service/SomeServiceWithPrivateSetter.php', []];
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
