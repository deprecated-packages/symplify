<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\RequireSpecificReturnTypeOverAbstractRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Explicit\RequireSpecificReturnTypeOverAbstractRule\Source\SpecificControl;

final class SkipSpecificReturnType
{
    public function create(): SpecificControl
    {
        return new SpecificControl();
    }
}
