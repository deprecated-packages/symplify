<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Domain\RequireExceptionNamespaceRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Domain\RequireExceptionNamespaceRule;

/**
 * @extends RuleTestCase<RequireExceptionNamespaceRule>
 */
final class RequireExceptionNamespaceRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/MisslocatedException.php', [[RequireExceptionNamespaceRule::ERROR_MESSAGE, 7]]];
        yield [__DIR__ . '/Fixture/Exception/SkipCorrectException.php', []];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(RequireExceptionNamespaceRule::class);
    }
}
