<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFactoryInConstructorRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\NoFactoryInConstructorRule;

/**
 * @extends RuleTestCase<NoFactoryInConstructorRule>
 */
final class NoFactoryInConstructorRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/ValueObject/Skip.php', []];
        yield [__DIR__ . '/Fixture/SkipException.php', []];
        yield [__DIR__ . '/Fixture/SkipEntityRepositoryFactory.php', []];
        yield [__DIR__ . '/Fixture/SkipArrayDimAssign.php', []];
        yield [__DIR__ . '/Fixture/SkipWithConstructorWithoutFactory.php', []];
        yield [__DIR__ . '/Fixture/SkipWithConstructorUseMethodCallFromCurrentObject.php', []];

        yield [
            __DIR__ . '/Fixture/WithConstructorWithFactoryWithAssignment.php',
            [[NoFactoryInConstructorRule::ERROR_MESSAGE, 13]],
        ];
        yield [
            __DIR__ . '/Fixture/WithConstructorWithFactoryWithMutliAssignment.php',
            [[NoFactoryInConstructorRule::ERROR_MESSAGE, 11]],
        ];

        yield [__DIR__ . '/Fixture/SkipParameterProvider.php', []];

        yield [__DIR__ . '/Fixture/SkipValueObject.php', []];
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
        return self::getContainer()->getByType(NoFactoryInConstructorRule::class);
    }
}
