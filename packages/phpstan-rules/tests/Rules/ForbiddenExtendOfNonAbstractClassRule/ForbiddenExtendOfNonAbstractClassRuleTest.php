<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenExtendOfNonAbstractClassRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenExtendOfNonAbstractClassRule;

/**
 * @extends RuleTestCase<ForbiddenExtendOfNonAbstractClassRule>
 */
final class ForbiddenExtendOfNonAbstractClassRuleTest extends RuleTestCase
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
        yield [
            __DIR__ . '/Fixture/ClassExtendingNonAbstractClass.php',
            [[ForbiddenExtendOfNonAbstractClassRule::ERROR_MESSAGE, 9]], ];

        yield [__DIR__ . '/Fixture/SkipVendorBasedClasses.php', []];
        yield [__DIR__ . '/Fixture/SkipClassExtendingAbstractClass.php', []];
        yield [__DIR__ . '/Fixture/SkipException.php', []];
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
        return self::getContainer()->getByType(ForbiddenExtendOfNonAbstractClassRule::class);
    }
}
