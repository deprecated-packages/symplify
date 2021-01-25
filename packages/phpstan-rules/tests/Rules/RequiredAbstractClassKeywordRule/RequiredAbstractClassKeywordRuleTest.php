<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\RequiredAbstractClassKeywordRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\RequiredAbstractClassKeywordRule;

final class RequiredAbstractClassKeywordRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/AbstractClass.php', []];
        yield [__DIR__ . '/Fixture/SkipSomeClass.php', []];

        yield [
            __DIR__ . '/Fixture/AbstractPrefixOnNonAbstractClass.php',
            [[RequiredAbstractClassKeywordRule::ERROR_MESSAGE, 7]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            RequiredAbstractClassKeywordRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
