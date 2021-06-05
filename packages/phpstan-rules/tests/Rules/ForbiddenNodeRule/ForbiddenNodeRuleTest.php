<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNodeRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenNodeRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenNodeRule>
 */
final class ForbiddenNodeRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipCommentIntentionally.php', []];

        $errorMessage = sprintf(ForbiddenNodeRule::ERROR_MESSAGE, 'empty($value)');
        yield [__DIR__ . '/Fixture/EmptyCall.php', [[$errorMessage, 11]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(ForbiddenNodeRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
