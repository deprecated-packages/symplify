<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\PreferredClassRule;

use DateTime as NativeDateTime;
use Iterator;
use Nette\Utils\DateTime;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\PreferredClassRule;
use Symplify\CodingStandard\Tests\Rules\PreferredClassRule\Fixture\SkipPrefferedExtendingTheOldOne;
use Symplify\CodingStandard\Tests\Rules\PreferredClassRule\Source\AbstractNotWhatYouWant;

final class PreferredClassRuleTest extends RuleTestCase
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
        $errorMessage = sprintf(PreferredClassRule::ERROR_MESSAGE, NativeDateTime::class, DateTime::class);
        yield [__DIR__ . '/Fixture/ClassUsingOld.php', [[$errorMessage, 13]]];

        yield [__DIR__ . '/Fixture/ClassExtendingOld.php', [[$errorMessage, 9]]];

        yield [__DIR__ . '/Fixture/ClassMethodParameterUsingOld.php', [[$errorMessage, 11]]];

        yield [__DIR__ . '/Fixture/SkipPrefferedExtendingTheOldOne.php', []];
    }

    protected function getRule(): Rule
    {
        return new PreferredClassRule([
            NativeDateTime::class => DateTime::class,
            AbstractNotWhatYouWant::class => SkipPrefferedExtendingTheOldOne::class,
        ]);
    }
}
