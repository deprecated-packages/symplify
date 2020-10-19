<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenMethodCallInIfRule\Fixture;

class SkipMethodCallWithBooleanPrefix
{
    public function isSkipped($arg)
    {
        return [];
    }

    public function execute($arg)
    {
        $obj = new self();
        if ($obj->isSkipped($arg) === []) {

        } elseif ($obj->isSkipped($arg) !== []) {

        }
    }

}
