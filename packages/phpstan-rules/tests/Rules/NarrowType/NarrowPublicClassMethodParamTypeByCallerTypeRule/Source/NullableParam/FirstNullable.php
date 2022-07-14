<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Source\NullableParam;

use PhpParser\Node;
use Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Fixture\SkipNullableCompare;

final class FirstNullable
{
    public function run(SkipNullableCompare $skipNullableCompare, ?Node $node): void
    {
        $skipNullableCompare->callNode($node);
    }
}
