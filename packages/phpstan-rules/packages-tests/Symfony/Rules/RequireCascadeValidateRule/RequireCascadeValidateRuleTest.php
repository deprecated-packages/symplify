<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Symfony\Rules\RequireCascadeValidateRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Symfony\Rules\RequireCascadeValidateRule;

/**
 * @extends RuleTestCase<RequireCascadeValidateRule>
 */
final class RequireCascadeValidateRuleTest extends RuleTestCase
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
        yield [__DIR__ . '/Fixture/SomeFormType.php', [
            [sprintf(RequireCascadeValidateRule::ERROR_MESSAGE, 'anotherPropertyObject'), 12],
            [sprintf(RequireCascadeValidateRule::ERROR_MESSAGE, 'typedProperty'), 14],
        ]];

        yield [__DIR__ . '/Fixture/NullablePropertyFormType.php', [
            [sprintf(RequireCascadeValidateRule::ERROR_MESSAGE, 'nullableProperty'), 12],
        ]];

        yield [__DIR__ . '/Fixture/CollectionPropertyFormType.php', [
            [sprintf(RequireCascadeValidateRule::ERROR_MESSAGE, 'anotherPropertyObjects'), 12],
        ]];

        yield [__DIR__ . '/Fixture/SkipFormTypeWithAnnotation.php', []];
        yield [__DIR__ . '/Fixture/SkipNoDataClass.php', []];
        yield [__DIR__ . '/Fixture/SkipDateTimeClass.php', []];
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
        return self::getContainer()->getByType(RequireCascadeValidateRule::class);
    }
}
