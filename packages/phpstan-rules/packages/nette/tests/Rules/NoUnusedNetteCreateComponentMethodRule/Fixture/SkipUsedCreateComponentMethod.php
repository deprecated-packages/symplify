<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\NoUnusedNetteCreateComponentMethodRule\Fixture;

use Nette\Application\UI\Presenter;

final class SkipUsedCreateComponentMethod extends Presenter
{
    protected function createComponentSomeComponent()
    {
    }
}
