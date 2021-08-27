<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\NoNetteRenderUnusedVariableRule\Fixture;

use Nette\Application\UI\Control;

final class SkipIncludeVariable extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/../Source/template_with_include.latte', [
            'use_me' => 'some_value'
        ]);
    }
}
