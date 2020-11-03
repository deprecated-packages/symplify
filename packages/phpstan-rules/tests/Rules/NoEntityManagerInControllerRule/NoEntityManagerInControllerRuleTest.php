<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoEntityManagerInControllerRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoEntityManagerInControllerRule;

final class NoEntityManagerInControllerRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [
            __DIR__ . '/Fixture/UsingEntityManagerController.php',
            [[NoEntityManagerInControllerRule::ERROR_MESSAGE, 17]],
        ];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoEntityManagerInControllerRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
