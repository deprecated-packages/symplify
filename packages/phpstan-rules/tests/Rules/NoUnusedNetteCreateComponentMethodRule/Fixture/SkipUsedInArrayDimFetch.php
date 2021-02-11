<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoUnusedNetteCreateComponentMethodRule\Fixture;

use Nette\Application\UI\Presenter;

final class SkipUsedInArrayDimFetch extends Presenter
{
    protected function createComponentWhatever()
    {
    }

    public function renderDefault()
    {
        return $this['whatever'];
    }
}
