<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckRequiredMethodNamingRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\CheckRequiredMethodNamingRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<CheckRequiredMethodNamingRule>
 */
final class CheckRequiredMethodNamingRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param string[] $filePaths
     * @param array<int|string> $expectedErrorMessagesWithLines
     */
    public function testRule(array $filePaths, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse($filePaths, $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [[__DIR__ . '/Fixture/SkipWithoutRequired.php'], []];
        yield [[__DIR__ . '/Fixture/SkipCorretName.php'], []];
        yield [[__DIR__ . '/Fixture/SkipWithInjectAttributeCorrect.php'], []];

        yield [[__DIR__ . '/Fixture/WithInject.php'], [[CheckRequiredMethodNamingRule::ERROR_MESSAGE, 12]]];
        yield [[__DIR__ . '/Fixture/WithInjectAttribute.php'], [[CheckRequiredMethodNamingRule::ERROR_MESSAGE, 11]]];

        yield [[__DIR__ . '/Fixture/WithRequiredNotAutowire.php'],
            [[CheckRequiredMethodNamingRule::ERROR_MESSAGE, 12]], ];
        yield [[__DIR__ . '/Fixture/WithRequiredAttributeNotAutowire.php'],
            [[CheckRequiredMethodNamingRule::ERROR_MESSAGE, 11]], ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckRequiredMethodNamingRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
