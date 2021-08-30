<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\NoNetteRenderUnusedVariableRule\Fixture;

use Nette\Application\UI\Control;

final class SkipUsedInInlineMacro extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/../Source/used_in_inline_macro.latte', [
            'value' => 'some_value'
        ]);
    }
}
