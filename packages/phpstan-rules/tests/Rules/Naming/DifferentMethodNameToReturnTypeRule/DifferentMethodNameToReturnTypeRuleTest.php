<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Naming\DifferentMethodNameToReturnTypeRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Naming\DifferentMethodNameToReturnTypeRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<DifferentMethodNameToReturnTypeRule>
 */
final class DifferentMethodNameToReturnTypeRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SameName.php', [[DifferentMethodNameToReturnTypeRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/SkipDifferentName.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            DifferentMethodNameToReturnTypeRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
