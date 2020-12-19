<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckTypehintCallerTypeRule\Fixture;

use PHPStan\Type\MixedType;

class SkipOptedOut
{
    public function run(\PHPStan\Type\Type $type)
    {
        if ($type instanceof MixedType) {
            return;
        }

        $this->isCheck($type);
    }

    private function isCheck(\PHPStan\Type\Type $type)
    {
    }
}
