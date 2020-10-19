<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenMethodOrFuncCallInIfRule\Fixture;

class WithMethodCallWithoutParameter
{
    public function getData()
    {
        return [];
    }

    public function execute()
    {
        if ($this->getData() === []) {

        } elseif ($this->getData() !== []) {

        }
    }

}
