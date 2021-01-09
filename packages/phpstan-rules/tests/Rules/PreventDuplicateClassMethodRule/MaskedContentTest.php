<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule;

use Iterator;
use PHPStan\Rules\Rule;
use Symplify\PHPStanExtensions\Testing\AbstractServiceAwareRuleTestCase;
use Symplify\PHPStanRules\Rules\PreventDuplicateClassMethodRule;
use Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture\FirstClass;
use Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture\FirstClassWithParameter;

final class MaskedContent extends AbstractServiceAwareRuleTestCase
{
    /**
     * @param string[] $filePaths
     * @dataProvider provideData()
     */
    public function testRule(array $filePaths, array $expectedErrorMessagesWithLines): void
    {
        $this->analyse($filePaths, $expectedErrorMessagesWithLines);
    }

    public function provideData(): Iterator
    {
        $errorMessage = sprintf(PreventDuplicateClassMethodRule::ERROR_MESSAGE, 'method', FirstClassWithParameter::class);
        yield [[
            __DIR__ . '/Fixture/FirstClassWithParameter.php',
            __DIR__ . '/Fixture/SecondClassDuplicateFirstClassWithParameterMethod.php',
        ], [[$errorMessage, 12]]];
    }

    protected function getRule(): Rule
    {
        return $this->getRuleFromConfig(
            PreventDuplicateClassMethodRule::class,
            __DIR__ . '/../../../config/symplify-rules.neon'
        );
    }
}
