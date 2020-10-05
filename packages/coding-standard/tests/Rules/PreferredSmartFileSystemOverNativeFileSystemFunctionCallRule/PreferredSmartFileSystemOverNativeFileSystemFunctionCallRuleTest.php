<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\PreferredSmartFileSystemOverNativeFileSystemFunctionCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\PreferredSmartFileSystemOverNativeFileSystemFunctionCallRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\SmartFileSystem\SmartFileSystem;

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
            SmartFileSystem::class,
            'readFile',
            'file_get_contents'
        );

        yield [__DIR__ . '/Fixture/FileGetContentsCalled.php', [[$errorMessage, 11]]];

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
