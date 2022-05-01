<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\DibiMaskMatchesVariableTypeRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Nette\Rules\DibiMaskMatchesVariableTypeRule;

/**
 * @extends RuleTestCase<DibiMaskMatchesVariableTypeRule>
 */
final class DibiMaskMatchesVariableTypeRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(DibiMaskMatchesVariableTypeRule::ERROR_MESSAGE, '%v', 'string', 'array');
        yield [__DIR__ . '/Fixture/InvalidType.php', [[$errorMessage, 13]]];

        $errorMessage = sprintf(DibiMaskMatchesVariableTypeRule::ERROR_MESSAGE, '%in', 'string', 'array');
        yield [__DIR__ . '/Fixture/InvalidAssignType.php', [[$errorMessage, 12]]];

        yield [__DIR__ . '/Fixture/InvalidArray.php', [[$errorMessage, 12]]];

        yield [__DIR__ . '/Fixture/NotNullableArray.php', [[$errorMessage, 13]]];

        yield [__DIR__ . '/Fixture/SkipValidAssignType.php', []];
        yield [__DIR__ . '/Fixture/SkipValidArray.php', []];
        yield [__DIR__ . '/Fixture/SkipMatchingType.php', []];

        yield [__DIR__ . '/Fixture/SkipMaskNonDibi.php', []];
        yield [__DIR__ . '/Fixture/SkipNullableArray.php', []];

        yield [__DIR__ . '/Fixture/SkipArrayUnionArray.php', []];
        yield [__DIR__ . '/Fixture/SkipValidJustUnionType.php', []];
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
        return self::getContainer()->getByType(DibiMaskMatchesVariableTypeRule::class);
    }
}
