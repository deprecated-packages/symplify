<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNewOutsideFactoryRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoNewOutsideFactoryRule;
use Symplify\PHPStanRules\Tests\Rules\NoNewOutsideFactoryRule\Source\SomeValueObject;

final class NoNewOutsideFactoryRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        $errorMessage = sprintf(NoNewOutsideFactoryRule::ERROR_MESSAGE, SomeValueObject::class);
        yield [__DIR__ . '/Fixture/SomeNew.php', [[$errorMessage, 14]]];

        yield [__DIR__ . '/Fixture/SkipNonReturned.php', []];
        yield [__DIR__ . '/Fixture/SkipReturnedDifferentNode.php', []];

        yield [__DIR__ . '/Fixture/SkipException.php', []];
        yield [__DIR__ . '/Fixture/SkipSuffixTest.php', []];
        yield [__DIR__ . '/Fixture/SkipReturnVoid.php', []];
        yield [__DIR__ . '/Fixture/SkipNode.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoNewOutsideFactoryRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
