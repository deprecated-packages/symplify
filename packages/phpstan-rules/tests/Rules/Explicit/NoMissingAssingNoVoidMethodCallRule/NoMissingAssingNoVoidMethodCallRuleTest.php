<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMissingAssingNoVoidMethodCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\Explicit\NoMissingAssingNoVoidMethodCallRule;

/**
 * @extends RuleTestCase<NoMissingAssingNoVoidMethodCallRule>
 */
final class NoMissingAssingNoVoidMethodCallRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/ReturnedNoVoid.php', [[NoMissingAssingNoVoidMethodCallRule::ERROR_MESSAGE, 11]]];

<<<<<<< HEAD
=======
        yield [__DIR__ . '/Fixture/SkipTokens.php', []];
>>>>>>> [PHPStanRules] Enable the no assign fluent
        yield [__DIR__ . '/Fixture/SkipReturnedNoVoid.php', []];
        yield [__DIR__ . '/Fixture/SkipFluentOutsideOnPurpose.php', []];
        yield [__DIR__ . '/Fixture/SkipSymfonyContainerConfigurator.php', []];
        yield [__DIR__ . '/Fixture/SkipDefaultSymfonyAutowire.php', []];
        yield [__DIR__ . '/Fixture/SkipNodeTraverser.php', []];
<<<<<<< HEAD
=======
        yield [__DIR__ . '/Fixture/SkipCommandOptions.php', []];
>>>>>>> [PHPStanRules] Enable the no assign fluent
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
        return self::getContainer()->getByType(NoMissingAssingNoVoidMethodCallRule::class);
    }
}
