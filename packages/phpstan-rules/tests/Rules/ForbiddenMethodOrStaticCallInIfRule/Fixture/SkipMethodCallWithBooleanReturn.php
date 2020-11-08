<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodOrStaticCallInIfRule\Fixture;

class SkipMethodCallWithBooleanReturn
{
    public function isSkipped($arg): bool
    {
        return true;
    }

    public function execute($arg)
    {
        $obj = new self();
        if ($obj->isSkipped($arg) === []) {

        } elseif ($obj->isSkipped($arg) !== []) {

        }
    }

}
