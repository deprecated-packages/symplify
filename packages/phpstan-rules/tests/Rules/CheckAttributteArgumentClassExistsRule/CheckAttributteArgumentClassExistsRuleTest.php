<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckAttributteArgumentClassExistsRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\CheckAttributteArgumentClassExistsRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<CheckAttributteArgumentClassExistsRule>
 */
final class CheckAttributteArgumentClassExistsRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipExistingClassAttributeArgument.php', []];

        yield [__DIR__ . '/Fixture/SomeClassWithAttributeArgumentMissingClass.php', [
            [CheckAttributteArgumentClassExistsRule::ERROR_MESSAGE, 9],
        ]];
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
        return self::getContainer()->getByType(CheckAttributteArgumentClassExistsRule::class);
    }
}
