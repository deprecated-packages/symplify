<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoParentMethodCallOnEmptyStatementInParentMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoParentMethodCallOnEmptyStatementInParentMethodRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoParentMethodCallOnEmptyStatementInParentMethodRule>
 *
 * @requires PHP 8.0
 */
final class Php80Test extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<string[]|array<int, mixed[]>>
     */
    public function provideData(): Iterator
    {
        yield [__DIR__ . '/FixturePhp80/SkipPromotedParentProperty.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoParentMethodCallOnEmptyStatementInParentMethodRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
