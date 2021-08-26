<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\RequireSpecificReturnTypeOverAbstractRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Explicit\RequireSpecificReturnTypeOverAbstractRule\Source\AbstractControl;
use Symplify\PHPStanRules\Tests\Rules\Explicit\RequireSpecificReturnTypeOverAbstractRule\Source\SpecificControl;

final class SomeAbstractReturnType
{
    public function create(): AbstractControl
    {
        return new SpecificControl();
    }
}
