<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenComplexForeachIfExprRule\Fixture;

class SkipWithoutMethodCall
{
    public function execute($arg)
    {
        $data = [];
        if ($data === []) {

        } elseif ($data !== []) {

        }
    }

}
