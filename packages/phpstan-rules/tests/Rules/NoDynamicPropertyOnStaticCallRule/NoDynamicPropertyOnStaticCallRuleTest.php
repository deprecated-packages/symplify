<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicPropertyOnStaticCallRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\NoDynamicPropertyOnStaticCallRule;

final class NoDynamicPropertyOnStaticCallRuleTest extends AbstractServiceAwareRuleTestCase
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
        yield [__DIR__ . '/Fixture/SkipNonDynamicProperty.php', []];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            NoDynamicPropertyOnStaticCallRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
