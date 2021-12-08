<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenParamTypeRemovalRule\Fixture;

class HasDifferentParameterWithInterfaceMethod implements AnInterface, AnInterfaceOther
{
    public function execute($string, $int)
    {
    }

    public function run($stdClass)
    {
    }
}
