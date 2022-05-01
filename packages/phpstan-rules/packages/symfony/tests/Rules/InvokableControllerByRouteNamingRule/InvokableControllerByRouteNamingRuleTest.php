<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\InvokableControllerByRouteNamingRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanExtensions\Testing\Contract\RuleRequiresNodeConnectingVisitorInterface;
use Symplify\PHPStanRules\Symfony\Rules\InvokableControllerByRouteNamingRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<InvokableControllerByRouteNamingRule>
 */
final class InvokableControllerByRouteNamingRuleTest extends AbstractServiceAwareRuleTestCase implements RuleRequiresNodeConnectingVisitorInterface
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
        yield [__DIR__ . '/Fixture/SkipRandomPublicMethodController.php', []];
        yield [__DIR__ . '/Fixture/SkipAtAnnotationController.php', []];
        yield [__DIR__ . '/Fixture/SkipValidController.php', []];
        yield [__DIR__ . '/Fixture/SkipValid.php', []];
        yield [__DIR__ . '/Fixture/DifferentNameController.php', [
            [InvokableControllerByRouteNamingRule::ERROR_MESSAGE, 12],
        ]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            InvokableControllerByRouteNamingRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
