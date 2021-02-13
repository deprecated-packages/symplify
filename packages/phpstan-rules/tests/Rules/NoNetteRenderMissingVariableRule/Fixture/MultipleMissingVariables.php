<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNetteRenderMissingVariableRule\Fixture;

use Nette\Application\UI\Control;

final class MultipleMissingVariables extends Control
{
    public function render()
    {
        $this->template->render(__DIR__ . '/../Source/multiple_variables.latte');
    }
}
