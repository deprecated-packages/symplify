<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PrefferedMethodCallOverIdenticalCompareRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\PrefferedMethodCallOverIdenticalCompareRule;
use Symplify\SmartFileSystem\SmartFileSystem;

final class PrefferedMethodCallOverIdenticalCompareRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param mixed[] $expectedErrorMessagesWithLines
     */
    public function testRule(string $filePath, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse([$filePath], $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        $errorMessage = sprintf(
            PrefferedMethodCallOverIdenticalCompareRule::ERROR_MESSAGE,
            'Rector\Core\Rector\AbstractRector->isName',
            'Rector\Core\Rector\AbstractRector->getName'
        );
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            PrefferedMethodCallOverIdenticalCompareRule::class,
            __DIR__ . '/config/configured_rule.neon'
        );
    }
}
