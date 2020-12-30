<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNullableArrayPropertyRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoNullableArrayPropertyRule;

final class Php74Test extends AbstractServiceAwareRuleTestCase
{
    /**
     * @requires PHP 7.4
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipNotNullable.php', []];
        yield [__DIR__ . '/FixturePhp74/SkipNotArray.php', []];
        yield [__DIR__ . '/FixturePhp74/SkipClassNameProperty.php', []];
        yield [__DIR__ . '/FixturePhp74/NullableArrayProperty.php', [[NoNullableArrayPropertyRule::ERROR_MESSAGE, 9]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoNullableArrayPropertyRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
