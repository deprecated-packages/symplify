<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodOrStaticCallInForeachRule\Fixture;

class SkipIfWithMethodCallWithoutParameter
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
