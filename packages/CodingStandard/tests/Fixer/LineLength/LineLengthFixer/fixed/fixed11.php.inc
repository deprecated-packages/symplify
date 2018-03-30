<?php declare(strict_types=1);

class SomeClass
{
    public function someFunction()
    {
        return $this->assertSame($parameterProvider->provide(), [
                'one' => 1,
                'two' => 2,
            ]);
    }
}
