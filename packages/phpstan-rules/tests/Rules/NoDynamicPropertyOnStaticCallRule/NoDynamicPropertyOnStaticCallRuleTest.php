<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicPropertyOnStaticCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoDynamicPropertyOnStaticCallRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoDynamicPropertyOnStaticCallRule>
 */
final class NoDynamicPropertyOnStaticCallRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/DynamicMethodCall.php', [[NoDynamicPropertyOnStaticCallRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/DynamicPropertyCall.php', [[NoDynamicPropertyOnStaticCallRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/SkipNonDynamicPropertyCall.php', []];
        yield [__DIR__ . '/Fixture/SkipNonDynamicMethodCall.php', []];
        yield [__DIR__ . '/Fixture/SkipSelfStatic.php', []];
        yield [__DIR__ . '/Fixture/SkipAbstractClassCall.php', []];
        yield [__DIR__ . '/Fixture/SkipUnionTypes.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoDynamicPropertyOnStaticCallRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
