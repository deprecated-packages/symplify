<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNetteRenderUnusedVariableRule\Fixture;

use Nette\Application\UI\Control;

final class RenderWithUnusedVariable extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/../Source/some_template.latte', [
            'unused_variable' => 'some_value'
        ]);
    }
}
