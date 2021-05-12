<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNewInMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\ForbiddenNewInMethodRule;
use Symplify\PHPStanRules\Tests\Rules\ForbiddenNewInMethodRule\Fixture\DefinedInterfaceAndParentClass;
use Symplify\PHPStanRules\Tests\Rules\ForbiddenNewInMethodRule\Fixture\HasNewInMethod;

/**
 * @extends AbstractServiceAwareRuleTestCase<ForbiddenNewInMethodRule>
 */
final class ForbiddenNewInMethodRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipAnonymousClass.php', []];
        yield [__DIR__ . '/Fixture/SkipNoNewInMethod.php', []];

        yield [__DIR__ . '/Fixture/HasNewInMethod.php', [
            [sprintf(ForbiddenNewInMethodRule::ERROR_MESSAGE, HasNewInMethod::class, 'run'), 9],
        ]];
        yield [__DIR__ . '/Fixture/DefinedInterfaceAndParentClass.php', [
            [sprintf(ForbiddenNewInMethodRule::ERROR_MESSAGE, DefinedInterfaceAndParentClass::class, 'run'), 9],
        ]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(ForbiddenNewInMethodRule::class, __DIR__ . '/config/configured_rule.neon');
    }
}
