<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReturnArrayVariableListRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoReturnArrayVariableListRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoReturnArrayVariableListRule>
 */
final class NoReturnArrayVariableListRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/ReturnVariables.php', [[NoReturnArrayVariableListRule::ERROR_MESSAGE, 11]]];

        yield [__DIR__ . '/Fixture/Enum/SkipEnum.php', []];
        yield [__DIR__ . '/Fixture/SkipReturnOne.php', []];
        yield [__DIR__ . '/Fixture/SkipNews.php', []];
        yield [__DIR__ . '/Fixture/ValueObject/SkipValueObject.php', []];
        yield [__DIR__ . '/Fixture/SkipParentMethod.php', []];
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
        return self::getContainer()->getByType(NoReturnArrayVariableListRule::class);
    }
}
