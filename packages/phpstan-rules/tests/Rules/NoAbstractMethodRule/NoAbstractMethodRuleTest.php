<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoAbstractMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoAbstractMethodRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoAbstractMethodRule>
 */
final class NoAbstractMethodRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SomeAbstractMethod.php', [[NoAbstractMethodRule::ERROR_MESSAGE, 9]]];
        yield [__DIR__ . '/Fixture/SkipNonAbstractMethod.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoAbstractMethodRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
