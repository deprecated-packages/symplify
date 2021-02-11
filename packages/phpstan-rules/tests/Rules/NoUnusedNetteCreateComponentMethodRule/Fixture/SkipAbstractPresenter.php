<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoUnusedNetteCreateComponentMethodRule\Fixture;

use Nette\Application\UI\Presenter;

abstract class SkipAbstractPresenter extends Presenter
{
    protected function createComponentWhateverElse()
    {
    }
}
