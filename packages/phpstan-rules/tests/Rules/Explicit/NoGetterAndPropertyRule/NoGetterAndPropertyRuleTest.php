<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoGetterAndPropertyRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Explicit\NoGetterAndPropertyRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoGetterAndPropertyRule>
 */
final class NoGetterAndPropertyRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        $boolGetterErrorMessage = \sprintf(NoGetterAndPropertyRule::ERROR_MESSAGE, 'enabled');
        yield [__DIR__ . '/Fixture/PublicAndIsser.php', [[$boolGetterErrorMessage, 7]]];

        $boolGetterErrorMessage = \sprintf(NoGetterAndPropertyRule::ERROR_MESSAGE, 'name');
        yield [__DIR__ . '/Fixture/SomeClassWithPublicAndGetter.php', [[$boolGetterErrorMessage, 7]]];

        yield [__DIR__ . '/Fixture/SkipPrivateMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipProtectedProperty.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(NoGetterAndPropertyRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
