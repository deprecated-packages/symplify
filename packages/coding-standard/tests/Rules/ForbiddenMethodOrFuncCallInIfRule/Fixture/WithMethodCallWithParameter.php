<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenMethodOrFuncCallInIfRule\Fixture;

class WithMethodCall
{
    public function getData($arg)
    {
        return [];
    }

    public function execute($arg)
    {
        if ($this->getData($arg) === []) {

        } elseif ($this->getData($arg) !== []) {

        }
    }

}
