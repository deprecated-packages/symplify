<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Nette\Rules\NoNetteTemplateVariableReadRule\Fixture;

use Nette\Application\UI\Control;

final class AvoidUnset extends Control
{
    public function run()
    {
        unset($this->template->whatever);
    }
}
