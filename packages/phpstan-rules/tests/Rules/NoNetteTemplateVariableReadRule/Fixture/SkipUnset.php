<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNetteTemplateVariableReadRule\Fixture;

use Nette\Application\UI\Control;

final class SkipUnset extends Control
{
    public function run()
    {
        unset($this->template->whatever);
    }
}
