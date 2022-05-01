<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckRequiredInterfaceInContractNamespaceRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\CheckRequiredInterfaceInContractNamespaceRule;

/**
 * @extends RuleTestCase<CheckRequiredInterfaceInContractNamespaceRule>
 */
final class CheckRequiredInterfaceInContractNamespaceRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/Contract/SkipInterfaceInContract.php', []];
        yield [
            __DIR__ . '/Fixture/AnInterfaceNotInContract.php',
            [[CheckRequiredInterfaceInContractNamespaceRule::ERROR_MESSAGE, 7]], ];
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
        return self::getContainer()->getByType(CheckRequiredInterfaceInContractNamespaceRule::class);
    }
}
