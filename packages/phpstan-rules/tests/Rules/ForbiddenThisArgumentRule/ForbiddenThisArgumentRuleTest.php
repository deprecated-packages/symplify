<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenThisArgumentRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenThisArgumentRule;

/**
 * @extends RuleTestCase<ForbiddenThisArgumentRule>
 */
final class ForbiddenThisArgumentRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipMethodExists.php', []];
        yield [__DIR__ . '/Fixture/SkipReflectionCalling.php', []];
        yield [__DIR__ . '/Fixture/SkipNotVariable.php', []];
        yield [__DIR__ . '/Fixture/SkipNotThis.php', []];
        yield [__DIR__ . '/Fixture/SkipExtendsKernel.php', []];

        yield [__DIR__ . '/Fixture/StaticCall.php', [[ForbiddenThisArgumentRule::ERROR_MESSAGE, 13]]];
        yield [__DIR__ . '/Fixture/ThisArgument.php', [[ForbiddenThisArgumentRule::ERROR_MESSAGE, 11]]];
        yield [__DIR__ . '/Fixture/ThisArgumentCopy.php', [[ForbiddenThisArgumentRule::ERROR_MESSAGE, 12]]];
        yield [__DIR__ . '/Fixture/ArgInFuncCall.php', [[ForbiddenThisArgumentRule::ERROR_MESSAGE, 11]]];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/configured_rule.neon'];
    }

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(ForbiddenThisArgumentRule::class);
    }
}
