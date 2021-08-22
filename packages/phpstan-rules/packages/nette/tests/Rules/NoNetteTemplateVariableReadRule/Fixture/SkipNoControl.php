<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Nette\NoNetteTemplateVariableReadRule\Fixture;

final class SkipNoControl
{
    public function render()
    {
        if ($this->template->key === 'value') {
            return;
        }
    }
}
