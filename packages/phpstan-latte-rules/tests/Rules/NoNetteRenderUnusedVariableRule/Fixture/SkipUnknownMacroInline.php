<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\Tests\Rules\NoNetteRenderUnusedVariableRule\Fixture;

use Nette\Application\UI\Control;

final class SkipUnknownMacroInline extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/../Source/unknown_macro_inline.latte', [
            'use_me_in_macro' => 'some_value'
        ]);
    }
}
