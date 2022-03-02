<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\RequireSpecificReturnTypeOverAbstractRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\Explicit\RequireSpecificReturnTypeOverAbstractRule;
use Symplify\PHPStanRules\Tests\Rules\Explicit\RequireSpecificReturnTypeOverAbstractRule\Source\SpecificControl;

/**
 * @extends AbstractServiceAwareRuleTestCase<RequireSpecificReturnTypeOverAbstractRule>
 */
final class RequireSpecificReturnTypeOverAbstractRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipSpecificReturnType.php', []];
        yield [__DIR__ . '/Fixture/SkipSomeContract.php', []];

        $errorMessage = sprintf(RequireSpecificReturnTypeOverAbstractRule::ERROR_MESSAGE, SpecificControl::class);
        yield [__DIR__ . '/Fixture/SomeAbstractReturnType.php', [[$errorMessage, 12]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequireSpecificReturnTypeOverAbstractRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
