<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNodeRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenNodeRule;

/**
 * @extends RuleTestCase<ForbiddenNodeRule>
 */
final class ForbiddenNodeRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return \Iterator<array<int, array<int[]|string[]>>|string[]>
     */
    public function provideData(): Iterator
    {
        $errorMessage = sprintf(ForbiddenNodeRule::ERROR_MESSAGE, 'empty($value)');
        yield [__DIR__ . '/Fixture/EmptyCall.php', [[$errorMessage, 11]]];
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
        return self::getContainer()->getByType(ForbiddenNodeRule::class);
    }
}
