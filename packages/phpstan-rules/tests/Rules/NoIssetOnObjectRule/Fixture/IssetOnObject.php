<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoIssetOnObjectRule\Fixture;

use stdClass;

final class IssetOnObject
{
    public function run()
    {
        if (mt_rand(0, 100)) {
            $object = new stdClass();
        }

        if (isset($object)) {
            return $object;
        }
    }
}
