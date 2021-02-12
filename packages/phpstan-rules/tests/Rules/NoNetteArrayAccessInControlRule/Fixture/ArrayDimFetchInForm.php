<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNetteArrayAccessInControlRule\Fixture;

use Nette\Application\UI\Form;

final class ArrayDimFetchInForm extends Form
{
    public function someAction()
    {
        return $this['some'];
    }
}
