<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoUnusedNetteCreateComponentMethodRule\Fixture;

use Nette\Application\UI\Presenter;

final class SkipUsedInThisGetComponent extends Presenter
{
    protected function createComponentAnotherComponent()
    {
    }

    public function renderDefault()
    {
        $this->getComponent('anotherComponent');
    }
}
