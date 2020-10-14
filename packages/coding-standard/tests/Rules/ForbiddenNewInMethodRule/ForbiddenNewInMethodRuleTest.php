<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenNewInMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Rules\ForbiddenNewInMethodRule;
use Symplify\CodingStandard\Tests\Rules\ForbiddenNewInMethodRule\Fixture\DefinedInterfaceAndParentClass;
use Symplify\CodingStandard\Tests\Rules\ForbiddenNewInMethodRule\Fixture\HasNewInMethod;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;

final class ForbiddenNewInMethodRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipAnonymousClass.php', []];
        yield [__DIR__ . '/Fixture/NoNewInMethod.php', []];

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
