<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNetteTemplateVariableReadRule\Fixture;

use Nette\Application\UI\Control;

final class SkipAssign extends Control
{
    public function render()
    {
        $this->template->key = 'value';
    }
}
