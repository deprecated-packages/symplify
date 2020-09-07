<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\SingleIndentationInMethodRule;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Symplify\CodingStandard\ObjectCalisthenics\Rules\NoShortNameRule;
use Symplify\CodingStandard\ObjectCalisthenics\Rules\SingleIndentationInMethodRule;

final class SingleIndentationInMethodRuleTest extends RuleTestCase
{
    public function test(): void
    {
        $this->analyse([__DIR__ . '/Fixture/ManyIndentations.php'], [
            [sprintf(NoShortNameRule::ERROR_MESSAGE, 'em'), 9],
            [sprintf(NoShortNameRule::ERROR_MESSAGE, 'YE'), 11],
        ]);
    }

    protected function getRule(): Rule
    {
        return new SingleIndentationInMethodRule();
    }
}
