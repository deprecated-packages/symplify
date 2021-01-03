<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenCallOnTypeRule;

use Iterator;
use Nette\Utils\Strings;
use PHPStan\Rules\Rule;
use Symfony\Component\DependencyInjection\Container;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenCallOnTypeRule;

final class ForbiddenCallOnTypeRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|string[]|int[]> $expectedErrorsWithLines
     */
    public function testRule(string $filePath, array $expectedErrorsWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorsWithLines);
    }

    public function provideData(): Iterator
    {
        yield [__DIR__ . '/Fixture/SkipCallOnOtherClass.php', []];

        yield [
            __DIR__ . '/Fixture/CallOnContainer.php',
            [[sprintf(ForbiddenCallOnTypeRule::ERROR_MESSAGE, Container::class), 23]],
        ];
        yield [
            __DIR__ . '/Fixture/CallOnNetteUtilsStrings.php',
            [[sprintf(ForbiddenCallOnTypeRule::ERROR_MESSAGE, Strings::class), 13]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(ForbiddenCallOnTypeRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
