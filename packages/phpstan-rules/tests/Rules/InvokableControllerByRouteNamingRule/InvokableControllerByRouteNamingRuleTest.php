<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\InvokableControllerByRouteNamingRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\InvokableControllerByRouteNamingRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<InvokableControllerByRouteNamingRule>
 */
final class InvokableControllerByRouteNamingRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @requires PHP 8.0
     * @dataProvider provideData()
     * @param array<int|string> $expectedErrorMessagesWithLines
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
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
