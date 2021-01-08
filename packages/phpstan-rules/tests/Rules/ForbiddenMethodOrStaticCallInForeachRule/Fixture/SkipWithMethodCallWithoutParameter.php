<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodOrStaticCallInForeachRule\Fixture;

class SkipWithMethodCallWithoutParameter
{
    public function getData()
    {
        return [];
    }

    public function execute()
    {
        foreach ($this->getData() as $key => $item) {

        }
    }

}
