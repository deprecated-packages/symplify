<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\PreferredSmartFileSystemOverNativeFileSystemFunctionCallRule;

use Iterator;
use Nette\Utils\Strings;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\PreferredSmartFileSystemOverNativeFileSystemFunctionCallRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class PreferredSmartFileSystemOverNativeFileSystemFunctionCallRuleTest extends AbstractServiceAwareRuleTestCase
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
            PreferredSmartFileSystemOverNativeFileSystemFunctionCallRule::ERROR_MESSAGE,
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
            PreferredSmartFileSystemOverNativeFileSystemFunctionCallRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
