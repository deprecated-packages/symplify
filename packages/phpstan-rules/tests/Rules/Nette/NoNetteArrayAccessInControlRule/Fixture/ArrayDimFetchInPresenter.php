<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Nette\NoNetteArrayAccessInControlRule\Fixture;

use Nette\Application\UI\Presenter;

final class ArrayDimFetchInPresenter extends Presenter
{
    public function someAction()
    {
        return $this['some'];
    }
}
