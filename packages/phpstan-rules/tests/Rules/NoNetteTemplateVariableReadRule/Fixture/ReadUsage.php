<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNetteTemplateVariableReadRule\Fixture;

use Nette\Application\UI\Control;

final class ReadUsage extends Control
{
    public function run()
    {
        if ($this->template->value) {
            return [];
        }
    }
}
