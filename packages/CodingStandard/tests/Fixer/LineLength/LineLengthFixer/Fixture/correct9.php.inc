<?php

declare(strict_types=1);

namespace Pehapkari\Website\Tests\Posts\Year2016\EventDispatcher;

final class EventDispatchingTest
{
    public function test(): void
    {
        $this->someMethod(function () {
           return 5;
        });
    }
}
