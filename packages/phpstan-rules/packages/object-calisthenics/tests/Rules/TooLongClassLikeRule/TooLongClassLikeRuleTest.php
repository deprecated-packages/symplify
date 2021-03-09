<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\TooLongClassLikeRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\ObjectCalisthenics\Rules\TooLongClassLikeRule;

final class TooLongClassLikeRuleTest extends AbstractServiceAwareRuleTestCase
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
     * @return Iterator<array<string|int[]|string[]>>
     */
    public function provideData(): Iterator
    {
        $errorMessage = sprintf(TooLongClassLikeRule::ERROR_MESSAGE, 'Class', 13, 10);
        yield [__DIR__ . '/Fixture/SuperLongClass.php', [[$errorMessage, 7]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(TooLongClassLikeRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
