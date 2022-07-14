<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Source\ExpectedNodeApi;

use PhpParser\Node\Stmt\Property;
use Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Fixture\SkipApiMarked;

final class CallWithProperty
{
    public function run(SkipApiMarked $skipApiMarked, Property $property): void
    {
        $skipApiMarked->callNode($property);
    }

}
