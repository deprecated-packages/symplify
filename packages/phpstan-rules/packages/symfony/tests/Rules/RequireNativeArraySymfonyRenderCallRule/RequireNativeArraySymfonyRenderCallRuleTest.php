<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\RequireNativeArraySymfonyRenderCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Symfony\Rules\RequireNativeArraySymfonyRenderCallRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<RequireNativeArraySymfonyRenderCallRule>
 */
final class RequireNativeArraySymfonyRenderCallRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipNoSecondArgument.php', []];
        yield [__DIR__ . '/Fixture/SkipCorrectControllerRender.php', []];

        yield [
            __DIR__ . '/Fixture/ParameterAsSecondArgument.php',
            [[RequireNativeArraySymfonyRenderCallRule::ERROR_MESSAGE, 17]], ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireNativeArraySymfonyRenderCallRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
