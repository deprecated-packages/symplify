<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNetteDoubleTemplateAssignRule\Fixture;

final class SkipNoPresenter
{
    public function render()
    {
        $this->template->key = '1000';
        $this->template->key = '100';
    }
}
