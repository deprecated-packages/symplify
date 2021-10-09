<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\Tests\Rules\NoNetteRenderUnusedVariableRule\Fixture;

use Nette\Application\UI\Control;

final class SkipFakingOpenCloseMacro extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/../Source/skip_faking_open_close_macro.latte', [
            'use_me_in_faked_macro' => 'some_value'
        ]);
    }
}
