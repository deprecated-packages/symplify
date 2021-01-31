<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNetteDoubleTemplateAssignRule\Fixture;

use Nette\Application\UI\Presenter;

final class SkipUniqueAssignPresenter extends Presenter
{
    public function render()
    {
        $this->template->key = '1000';
        $this->template->anotherKey = '100';
    }
}
