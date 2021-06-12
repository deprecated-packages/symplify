<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodOrStaticCallInForeachRule\Fixture;

class WithMethodCallWithParameterNotFromThis
{
    public function getData($arg)
    {
        return [];
    }

    public function execute($arg)
    {
        $obj = new self();
        if ($obj->getData($arg) === []) {

        } elseif ($obj->getData($arg) !== []) {

        }
    }

}
