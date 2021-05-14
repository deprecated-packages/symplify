<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\SuffixInterfaceRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\SuffixInterfaceRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<SuffixInterfaceRule>
 */
final class SuffixInterfaceRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipCorrectlyNameInterface.php', []];
        yield [__DIR__ . '/Fixture/InterfaceWithoutSuffix.php', [[SuffixInterfaceRule::ERROR_MESSAGE, 7]]];
        yield [__DIR__ . '/Fixture/NotAnInterface.php', [[SuffixInterfaceRule::ERROR_MESSAGE, 7]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            SuffixInterfaceRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
