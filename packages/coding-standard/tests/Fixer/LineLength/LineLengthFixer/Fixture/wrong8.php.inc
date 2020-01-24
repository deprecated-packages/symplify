<?php

class SomeClass
{
    public function someFunction()
    {
        $this->assertSame(['one' => 1, 'two' => 2, 'fooooooooooooooooooo' => 'baaaaaar'], $parameterProvider->provide());
    }
}

?>
-----
<?php

class SomeClass
{
    public function someFunction()
    {
        $this->assertSame(
            ['one' => 1, 'two' => 2, 'fooooooooooooooooooo' => 'baaaaaar'],
            $parameterProvider->provide()
        );
    }
}

?>
