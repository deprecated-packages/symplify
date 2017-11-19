<?php

namespace SomeNamespace;

use Carbon\Carbon;
use Phpml\Math\Set;

class SomeClass
{
    public function test()
    {
        $this->assertInstanceOf(\Phpml\Math\Set::class, $union);
    }

    /**
     * Get the Carbon instance for the current time.
     */
    public static function now(): \Carbon\Carbon
    {
        return Carbon::now('UTC');
    }
}
