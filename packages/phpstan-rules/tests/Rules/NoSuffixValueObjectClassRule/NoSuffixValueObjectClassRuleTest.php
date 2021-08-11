<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoSuffixValueObjectClassRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoSuffixValueObjectClassRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoSuffixValueObjectClassRule>
 */
final class NoSuffixValueObjectClassRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipNoValueObjectInNamespace.php', []];
        yield [__DIR__ . '/Fixture/ValueObject/SkipValueObjectWithoutValueObjectSuffix.php', []];

        $errorMessage = sprintf(NoSuffixValueObjectClassRule::ERROR_MESSAGE, 'MoneyValueObject');
        yield [__DIR__ . '/Fixture/ValueObject/MoneyValueObject.php', [[$errorMessage, 7]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoSuffixValueObjectClassRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
