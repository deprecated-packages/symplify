<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedMethodCallerRule\Fixture;

class HelloWorld
{
    public function run() {
        $hw = function():HelloWorld {
          return new HelloWorld();
        };
        
        $x = function () use ($hw) {
          $hw();
        };
    }
}
