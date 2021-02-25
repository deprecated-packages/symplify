<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNullablePropertyRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoNullablePropertyRule;

final class NoNullablePropertyRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipNoType.php', []];
        yield [__DIR__ . '/Fixture/NullableProperty.php', [[NoNullablePropertyRule::ERROR_MESSAGE, 11]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoNullablePropertyRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
