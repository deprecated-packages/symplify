<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\TooManyPropertiesRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\ObjectCalisthenics\Rules\TooManyPropertiesRule;

final class TooManyPropertiesRuleTest extends AbstractServiceAwareRuleTestCase
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
     * @return Iterator<array<int, array<int, array<int, int|string>>|string>>
     */
    public function provideData(): Iterator
    {
        $message = sprintf(TooManyPropertiesRule::ERROR_MESSAGE, 4, 3);
        yield [__DIR__ . '/Fixture/TooManyProperties.php', [[$message, 7]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(TooManyPropertiesRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
