<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNetteDoubleTemplateAssignRule\Fixture;

use Nette\Application\UI\Presenter;

final class SkipIfElseAssign extends Presenter
{
    public function render()
    {
        if (mt_rand(0, 1000)) {
            $this->template->key = '1000';
        } else {
            $this->template->key = '100';
        }
    }
}
