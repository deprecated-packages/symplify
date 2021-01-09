<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckRequiredMethodNamingRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\CheckRequiredMethodNamingRule;

final class CheckRequiredMethodNamingRuleTest extends AbstractServiceAwareRuleTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testRule(array $filePaths, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse($filePaths, $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        $errorMessage = sprintf(CheckRequiredMethodNamingRule::ERROR_MESSAGE, 'autowireRequiredByTrait');
        yield [
            [__DIR__ . '/Fixture/ClassUsingRequiredByTrait.php', __DIR__ . '/Fixture/RequiredByTrait.php'],
            [[$errorMessage, 12]],
        ];

        $errorMessage = sprintf(CheckRequiredMethodNamingRule::ERROR_MESSAGE, 'autowireRequiredByTraitCorrect');
        yield [
            [
                __DIR__ . '/Fixture/ClassUsingRequiredByTraitCorrect.php',
                __DIR__ . '/Fixture/RequiredByTraitCorrect.php',
            ],
            [[$errorMessage, 12]],
        ];

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
