<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Tests\Rules\NoNetteArrayAccessInControlRule\Fixture;

use Nette\Application\UI\Presenter;

final class ArrayDimFetchInControl extends Presenter
{
    public function someAction()
    {
        $values = $this['some'];
    }
}
