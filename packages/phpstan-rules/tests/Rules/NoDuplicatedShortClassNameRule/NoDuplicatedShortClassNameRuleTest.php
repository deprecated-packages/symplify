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
     * @param string[] $filePaths
     * @dataProvider provideData()
     * @param array<string|int> $expectedErrorMessagesWithLines
     */
    public function testRule(array $filePaths, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse($filePaths, $expectedErrorMessagesWithLines);
    }

    /**
     * @return Iterator<int, mixed[]>
     */
    public function provideData(): Iterator
    {
        $errorMessage = sprintf(
            NoDuplicatedShortClassNameRule::ERROR_MESSAGE,
            'AlreadyExistingShortName',
            implode('", "', [SecondAlreadyExistingShortName::class, AlreadyExistingShortName::class])
        );

        yield [
            [__DIR__ . '/Fixture/AlreadyExistingShortName.php', __DIR__ . '/Source/AlreadyExistingShortName.php'],
            [[$errorMessage, 7]], ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoDuplicatedShortClassNameRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
