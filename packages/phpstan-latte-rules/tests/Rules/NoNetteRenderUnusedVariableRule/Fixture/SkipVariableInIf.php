<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\Tests\Rules\NoNetteRenderUnusedVariableRule\Fixture;

use Nette\Application\UI\Control;

final class SkipVariableInIf extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/../Source/variable_with_if.latte', [
            'value' => 1000,
            'another_value' => 10000
        ]);
    }
}
