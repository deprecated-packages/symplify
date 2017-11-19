<?php

namespace SomeNamespace;

use Carbon\Carbon;
use Phpml\Math\Set;

class SomeClass
{
    public function test()
    {
        $this->assertInstanceOf(Set::class, $union);
    }

    /**
     * Get the Carbon instance for the current time.
     */
    public static function now(): Carbon
    {
        return Carbon::now('UTC');
    }
}
