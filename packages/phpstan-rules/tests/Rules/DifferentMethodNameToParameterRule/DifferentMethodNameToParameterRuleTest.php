<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\DifferentMethodNameToParameterRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\DifferentMethodNameToParameterRule;

final class DifferentMethodNameToParameterRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SameName.php', [[DifferentMethodNameToParameterRule::ERROR_MESSAGE, 9]]];
        yield [__DIR__ . '/Fixture/SkipDifferentName.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            DifferentMethodNameToParameterRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
