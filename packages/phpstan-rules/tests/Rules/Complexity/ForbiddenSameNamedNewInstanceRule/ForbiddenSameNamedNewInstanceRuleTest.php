<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedNewInstanceRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Complexity\ForbiddenSameNamedNewInstanceRule;

/**
 * @extends RuleTestCase<ForbiddenSameNamedNewInstanceRule>
 */
final class ForbiddenSameNamedNewInstanceRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipDifferentNames.php', []];
        yield [__DIR__ . '/Fixture/SkipNonObjectAssigns.php', []];

        $errorMessage = sprintf(ForbiddenSameNamedNewInstanceRule::ERROR_MESSAGE, '$someProduct');
        yield [__DIR__ . '/Fixture/SameObjectAssigns.php', [[$errorMessage, 14]]];
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
        return self::getContainer()->getByType(ForbiddenSameNamedNewInstanceRule::class);
    }
}
