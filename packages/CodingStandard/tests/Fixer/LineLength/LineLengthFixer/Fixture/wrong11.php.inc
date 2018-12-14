<?php

class SomeClass
{
    public function someFunction()
    {
        return $this->assertSame(
            $parameterProvider->provide(),
            [
                'one' => 1,
                'two' => 2,
            ]
        );
    }
}

?>
-----
<?php

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

?>
