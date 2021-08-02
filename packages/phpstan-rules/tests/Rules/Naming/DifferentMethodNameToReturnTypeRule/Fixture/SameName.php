<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Naming\DifferentMethodNameToReturnTypeRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Naming\DifferentMethodNameToReturnTypeRule\Source\Apple;

final class SameName
{
    public function apple(): Apple
    {
        return new Apple();
    }
}
