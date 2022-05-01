<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckClassNamespaceFollowPsr4Rule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\CheckClassNamespaceFollowPsr4Rule;

/**
 * @extends RuleTestCase<CheckClassNamespaceFollowPsr4Rule>
 */
final class CheckClassNamespaceFollowPsr4RuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipAnonymousClass.php', []];
        yield [__DIR__ . '/Fixture/SkipValidNamespaceInterface.php', []];

        yield [__DIR__ . '/Fixture/InvalidNamespaceClass.php', [
            [sprintf(CheckClassNamespaceFollowPsr4Rule::ERROR_MESSAGE, 'Foo\Bar'), 7],
        ]];
        yield [__DIR__ . '/Fixture/InvalidNamespaceInterface.php', [
            [sprintf(CheckClassNamespaceFollowPsr4Rule::ERROR_MESSAGE, 'Foo\Bar'), 7],
        ]];
        yield [__DIR__ . '/Fixture/InvalidNamespaceTrait.php', [
            [sprintf(CheckClassNamespaceFollowPsr4Rule::ERROR_MESSAGE, 'Foo\Bar'), 7],
        ]];

        $errorMessage = sprintf(
            CheckClassNamespaceFollowPsr4Rule::ERROR_MESSAGE,
            'Symplify\PHPStanRules\Tests\Rules\CheckClassNamespaceFollowPsr4Rule'
        );
        yield [__DIR__ . '/Fixture/MissingFixtureNamespaceClass.php', [[$errorMessage, 7]]];
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
        return self::getContainer()->getByType(CheckClassNamespaceFollowPsr4Rule::class);
    }
}
