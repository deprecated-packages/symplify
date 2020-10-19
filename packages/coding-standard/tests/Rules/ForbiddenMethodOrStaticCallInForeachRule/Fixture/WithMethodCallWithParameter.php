<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenMethodOrStaticCallInForeachRule\Fixture;

class WithMethodCall
{
    public function getData($arg)
    {
        return [];
    }

    public function execute($arg)
    {
        foreach ($this->getData($arg) as $key => $item) {

        }
    }

}