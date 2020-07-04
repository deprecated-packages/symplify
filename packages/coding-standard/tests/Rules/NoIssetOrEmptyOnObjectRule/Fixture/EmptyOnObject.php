<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoIssetOrEmptyOnObjectRule\Fixture;

use stdClass;

final class EmptyOnObject
{
    public function run()
    {
        if (mt_rand(0, 100)) {
            $object = new stdClass();
        }

        if (empty($object)) {
            return $object;
        }
    }
}
