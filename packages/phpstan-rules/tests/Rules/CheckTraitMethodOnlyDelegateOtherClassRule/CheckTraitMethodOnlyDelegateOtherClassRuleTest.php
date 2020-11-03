<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckTraitMethodOnlyDelegateOtherClassRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\CheckTraitMethodOnlyDelegateOtherClassRule;

final class CheckTraitMethodOnlyDelegateOtherClassRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/Delegate.php', []];
        yield [
            __DIR__ . '/Fixture/CallThisType.php',
            [[sprintf(CheckTraitMethodOnlyDelegateOtherClassRule::ERROR_MESSAGE, 'run'), 9]], ];
        yield [
            __DIR__ . '/Fixture/HasInstanceofCheck.php',
            [[sprintf(CheckTraitMethodOnlyDelegateOtherClassRule::ERROR_MESSAGE, 'run'), 9]], ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckTraitMethodOnlyDelegateOtherClassRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
