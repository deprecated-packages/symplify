<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\PrefferedMethodCallOverFuncCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\PrefferedMethodCallOverFuncCallRule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\SmartFileSystem\SmartFileSystem;

final class PrefferedMethodCallOverFuncCallRuleTest extends AbstractServiceAwareRuleTestCase
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
            PrefferedMethodCallOverFuncCallRule::ERROR_MESSAGE,
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
            PrefferedMethodCallOverFuncCallRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
