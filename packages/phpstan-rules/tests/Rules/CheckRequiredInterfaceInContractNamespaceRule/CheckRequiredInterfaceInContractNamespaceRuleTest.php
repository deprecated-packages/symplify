<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckRequiredInterfaceInContractNamespaceRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\CheckRequiredInterfaceInContractNamespaceRule;

final class CheckRequiredInterfaceInContractNamespaceRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorsWithLines
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

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckRequiredInterfaceInContractNamespaceRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
