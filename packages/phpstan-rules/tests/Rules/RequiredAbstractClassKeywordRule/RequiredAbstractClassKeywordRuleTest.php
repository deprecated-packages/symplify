<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequiredAbstractClassKeywordRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\RequiredAbstractClassKeywordRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<RequiredAbstractClassKeywordRule>
 */
final class RequiredAbstractClassKeywordRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/AbstractClass.php', []];
        yield [__DIR__ . '/Fixture/SkipSomeClass.php', []];

        yield [
            __DIR__ . '/Fixture/AbstractPrefixOnNonAbstractClass.php',
            [[RequiredAbstractClassKeywordRule::ERROR_MESSAGE, 7]],
        ];
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
        return self::getContainer()->getByType(RequiredAbstractClassKeywordRule::class);
    }
}
