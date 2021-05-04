<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenFuncCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenFuncCallRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenFuncCallRule>
 */
final class ForbiddenFuncCallRuleTest extends AbstractServiceAwareRuleTestCase
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
        $errorMessage = sprintf(ForbiddenFuncCallRule::ERROR_MESSAGE, 'dump');
        yield [__DIR__ . '/Fixture/DebugFuncCall.php', [[$errorMessage, 11]]];

        $errorMessage = sprintf(ForbiddenFuncCallRule::ERROR_MESSAGE, 'extract');
        yield [__DIR__ . '/Fixture/ExtractCall.php', [[$errorMessage, 11]]];

        $errorMessage = sprintf(ForbiddenFuncCallRule::ERROR_MESSAGE, 'curl_init');
        yield [__DIR__ . '/Fixture/CurlCall.php', [[$errorMessage, 11]]];

        $errorMessage = sprintf(ForbiddenFuncCallRule::ERROR_MESSAGE, 'property_exists');
        yield [__DIR__ . '/Fixture/PropertyExists.php', [[$errorMessage, 11]]];

        yield [__DIR__ . '/Fixture/SkipPropertyExistsOnXml.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(ForbiddenFuncCallRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
