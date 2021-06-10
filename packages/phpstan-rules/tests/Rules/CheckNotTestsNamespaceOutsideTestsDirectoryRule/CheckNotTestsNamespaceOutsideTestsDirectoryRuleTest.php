<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckNotTestsNamespaceOutsideTestsDirectoryRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\CheckNotTestsNamespaceOutsideTestsDirectoryRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<CheckNotTestsNamespaceOutsideTestsDirectoryRule>
 */
final class CheckNotTestsNamespaceOutsideTestsDirectoryRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    /**
     * @return Iterator<array<int, array|string>>
     */
    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/Tests/SkipTestsNamespaceInsideTestsDirectoryClass.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckNotTestsNamespaceOutsideTestsDirectoryRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
