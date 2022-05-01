<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoParentMethodCallOnEmptyStatementInParentMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoParentMethodCallOnEmptyStatementInParentMethodRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoParentMethodCallOnEmptyStatementInParentMethodRule>
 */
final class NoParentMethodCallOnEmptyStatementInParentMethodRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipObjectTypeExtends.php', []];
        yield [__DIR__ . '/Fixture/SkipException.php', []];
        yield [__DIR__ . '/Fixture/SkipNotCallParentMethod.php', []];
        yield [__DIR__ . '/Fixture/SkipCallParentMethodWithStatement.php', []];

        yield [
            __DIR__ . '/Fixture/CallParentMethod.php',
            [[NoParentMethodCallOnEmptyStatementInParentMethodRule::ERROR_MESSAGE, 11]],
        ];
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
        return self::getContainer()->getByType(NoParentMethodCallOnEmptyStatementInParentMethodRule::class);
    }
}
