<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenMethodCallInIfRule\Fixture;

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
