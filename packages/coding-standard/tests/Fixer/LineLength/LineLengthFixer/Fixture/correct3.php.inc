<?php

declare(strict_types=1);

class SomeClass
{
    public function someFunction()
    {
        $this->assertSame([
            'one' => 1,
            'two' => 2,
        ], $parameterProvider->provide());
    }
}
