<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNetteRenderMissingVariableRule\Fixture;

use Nette\Application\UI\Control;

final class SkipCompleteVariables extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/../Source/complete_variables.latte', [
            'used_this' => true
        ]);
    }
}
