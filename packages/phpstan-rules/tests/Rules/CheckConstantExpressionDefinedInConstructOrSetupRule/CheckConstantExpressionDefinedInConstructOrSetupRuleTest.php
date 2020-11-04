<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule;

final class CheckConstantExpressionDefinedInConstructOrSetupRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/InsideSetup.php', []];
        yield [__DIR__ . '/Fixture/InsideConstruct.php', []];
        yield [__DIR__ . '/Fixture/InsideOtherMethod.php', [[CheckConstantExpressionDefinedInConstructOrSetupRule::ERROR_MESSAGE, 13]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckConstantExpressionDefinedInConstructOrSetupRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
