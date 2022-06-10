<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Nette\Rules\NoNetteArrayAccessInControlRule\Fixture;

use Nette\Application\UI\Presenter;

final class ArrayDimFetchInControl extends Presenter
{
    public function someAction()
    {
        $values = $this['some'];
    }
}
