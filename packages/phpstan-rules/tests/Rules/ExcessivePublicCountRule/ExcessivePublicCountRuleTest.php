<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ExcessivePublicCountRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ExcessivePublicCountRule;

final class ExcessivePublicCountRuleTest extends AbstractServiceAwareRuleTestCase
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
        $message = sprintf(ExcessivePublicCountRule::ERROR_MESSAGE, 6, 5);
        yield [__DIR__ . '/Fixture/TooManyPublicElements.php', [[$message, 7]]];

        yield [__DIR__ . '/Fixture/SkipUnderLimit.php', []];
        yield [__DIR__ . '/Fixture/ValueObject/SkipConstantInValueObject.php', []];
        yield [__DIR__ . '/Fixture/SkipConstructorAndMagicMethods.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(ExcessivePublicCountRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
