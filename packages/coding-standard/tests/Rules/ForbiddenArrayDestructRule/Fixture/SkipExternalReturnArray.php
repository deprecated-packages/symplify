<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenArrayDestructRule\Fixture;

use Symfony\Component\DependencyInjection\Argument\BoundArgument;

final class SkipExternalReturnArray
{
    public function run()
    {
        $boundArgument = new BoundArgument('value');
        [$split, $value] = $boundArgument->getValues();
    }
}
