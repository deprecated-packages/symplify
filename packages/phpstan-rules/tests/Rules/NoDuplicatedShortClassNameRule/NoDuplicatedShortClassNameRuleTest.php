<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDuplicatedShortClassNameRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoDuplicatedShortClassNameRule;
use Symplify\PHPStanRules\Tests\Rules\NoDuplicatedShortClassNameRule\Fixture\AlreadyExistingShortName as SecondAlreadyExistingShortName;
use Symplify\PHPStanRules\Tests\Rules\NoDuplicatedShortClassNameRule\Source\AlreadyExistingShortName;

/**
 * @extends AbstractServiceAwareRuleTestCase<NoDuplicatedShortClassNameRule>
 */
final class NoDuplicatedShortClassNameRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<array<string|int[]|string[]>>
     */
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

        yield [__DIR__ . '/Fixture/AlreadyExistingShortName.php', [[$errorMessage, 7]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoDuplicatedShortClassNameRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
