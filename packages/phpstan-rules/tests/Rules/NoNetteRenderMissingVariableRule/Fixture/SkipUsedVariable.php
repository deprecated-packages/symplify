<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNetteRenderMissingVariableRule\Fixture;

use Nette\Application\UI\Control;

final class SkipUsedVariable extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/../Source/some_template_using_variable.latte', [
            'use_me' => 'some_value'
        ]);
    }
}
