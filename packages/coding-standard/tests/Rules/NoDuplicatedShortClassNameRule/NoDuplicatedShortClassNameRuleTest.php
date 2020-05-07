<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoDuplicatedShortClassNameRule;

use Iterator;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\Rules\NoDuplicatedShortClassNameRule;
use Symplify\CodingStandard\Tests\Rules\NoDuplicatedShortClassNameRule\Fixture\AlreadyExistingShortName as SecondAlreadyExistingShortName;
use Symplify\CodingStandard\Tests\Rules\NoDuplicatedShortClassNameRule\Source\AlreadyExistingShortName;

final class NoDuplicatedShortClassNameRuleTest extends RuleTestCase
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
        // make sure both files are loaded
        require __DIR__ . '/Fixture/AlreadyExistingShortName.php';
        require __DIR__ . '/Source/AlreadyExistingShortName.php';

        $errorMessage = sprintf(
            NoDuplicatedShortClassNameRule::ERROR_MESSAGE,
            'AlreadyExistingShortName',
            implode('", "', [SecondAlreadyExistingShortName::class, AlreadyExistingShortName::class])
        );
        yield [__DIR__ . '/Fixture/AlreadyExistingShortName.php', [$errorMessage, 7]];
    }

    protected function getRule(): Rule
    {
        return new NoDuplicatedShortClassNameRule();
    }
}
