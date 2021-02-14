<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNetteTemplateVariableReadRule\Fixture;

use Nette\Application\UI\Control;

final class SkipFlashes extends Control
{
    public function run()
    {
        return isset($this->template->flashes);
    }
}
