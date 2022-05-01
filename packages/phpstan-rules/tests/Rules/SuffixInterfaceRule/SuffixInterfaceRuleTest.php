<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\SuffixInterfaceRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\SuffixInterfaceRule;

/**
 * @extends RuleTestCase<SuffixInterfaceRule>
 */
final class SuffixInterfaceRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipCorrectlyNameInterface.php', []];
        yield [__DIR__ . '/Fixture/InterfaceWithoutSuffix.php', [[SuffixInterfaceRule::ERROR_MESSAGE, 7]]];
        yield [__DIR__ . '/Fixture/NotAnInterface.php', [[SuffixInterfaceRule::ERROR_MESSAGE, 7]]];
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
        return self::getContainer()->getByType(SuffixInterfaceRule::class);
    }
}
