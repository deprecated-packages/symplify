<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenParentClassRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\ForbiddenParentClassRule;
use Symplify\CodingStandard\Tests\Rules\ForbiddenParentClassRule\Fixture\AnotherForbiddenParent;
use Symplify\CodingStandard\Tests\Rules\ForbiddenParentClassRule\Fixture\ClassForbiddenParent;
use Symplify\CodingStandard\Tests\Rules\ForbiddenParentClassRule\Source\ForbiddenParent;
use Symplify\CodingStandard\Tests\Rules\ForbiddenParentClassRule\Source\SomeFnMatched;

final class ForbiddenParentClassRuleTest extends RuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], [$expectedErrorMessagesWithLines]);
    }

    public function provideData(): Iterator
    {
        $errorMessage = sprintf(
            ForbiddenParentClassRule::ERROR_MESSAGE,
            ClassForbiddenParent::class,
            ForbiddenParent::class
        );
        yield [__DIR__ . '/Fixture/ClassForbiddenParent.php', [$errorMessage, 9]];

        $errorMessage = sprintf(
            ForbiddenParentClassRule::ERROR_MESSAGE,
            AnotherForbiddenParent::class,
            SomeFnMatched::class
        );
        yield [__DIR__ . '/Fixture/AnotherForbiddenParent.php', [$errorMessage, 9]];
    }

    protected function getRule(): Rule
    {
        return new ForbiddenParentClassRule([ForbiddenParent::class, '*FnMatched']);
    }
}
