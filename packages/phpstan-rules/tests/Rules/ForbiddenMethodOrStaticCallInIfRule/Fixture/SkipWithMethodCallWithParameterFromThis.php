<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodOrStaticCallInIfRule\Fixture;

class SkipWithMethodCallWithParameterFromThis
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
