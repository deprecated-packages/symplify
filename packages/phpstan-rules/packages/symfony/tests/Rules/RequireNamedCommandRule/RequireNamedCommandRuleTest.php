<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\RequireNamedCommandRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Symfony\Rules\RequireNamedCommandRule;

/**
 * @extends RuleTestCase<RequireNamedCommandRule>
 */
final class RequireNamedCommandRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipNamedCommand.php', []];
        yield [__DIR__ . '/Fixture/SkipAbstractMissingNameCommand.php', []];
        yield [__DIR__ . '/Fixture/SkipAttributeNamedCommand.php', []];

        yield [__DIR__ . '/Fixture/MissingNameCommand.php', [[RequireNamedCommandRule::ERROR_MESSAGE, 11]]];
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
        return self::getContainer()->getByType(RequireNamedCommandRule::class);
    }
}
