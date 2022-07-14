<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Source;

use Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Fixture\PublicDoubleShot;

final class FirstCaller
{
    public function goForIt(PublicDoubleShot $publicDoubleShot)
    {
        $publicDoubleShot->callMeTwice(100);
    }
}
