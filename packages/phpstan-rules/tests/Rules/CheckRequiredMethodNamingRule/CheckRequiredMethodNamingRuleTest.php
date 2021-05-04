<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckRequiredMethodNamingRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\CheckRequiredMethodNamingRule;

/**
 * @extends AbstractServiceAwareRuleTestCase<CheckRequiredMethodNamingRule>
 */
final class CheckRequiredMethodNamingRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     * @param string[] $filePaths
     * @param array<int|string> $expectedErrorMessagesWithLines
     */
    public function testRule(array $filePaths, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse($filePaths, $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        yield [[__DIR__ . '/Fixture/SkipWithoutRequired.php'], []];
        yield [[__DIR__ . '/Fixture/SkipCorretName.php'], []];

        $errorMessage = sprintf(CheckRequiredMethodNamingRule::ERROR_MESSAGE, 'autowireWithRequiredNotAutowire');
        yield [[__DIR__ . '/Fixture/WithRequiredNotAutowire.php'], [[$errorMessage, 12]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            CheckRequiredMethodNamingRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
