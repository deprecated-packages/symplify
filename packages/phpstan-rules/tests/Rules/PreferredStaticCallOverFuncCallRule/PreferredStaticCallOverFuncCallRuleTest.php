<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferredStaticCallOverFuncCallRule;

use Iterator;
use Nette\Utils\Strings;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\PreferredStaticCallOverFuncCallRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<PreferredStaticCallOverFuncCallRule>
 */
final class PreferredStaticCallOverFuncCallRuleTest extends AbstractServiceAwareRuleTestCase
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
        $errorMessage = sprintf(
            PreferredStaticCallOverFuncCallRule::ERROR_MESSAGE,
            Strings::class,
            'match',
            'preg_match'
        );

        yield [__DIR__ . '/Fixture/PregMatchCalled.php', [[$errorMessage, 11]]];

        yield [__DIR__ . '/Fixture/SkipSelfCall.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            PreferredStaticCallOverFuncCallRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
