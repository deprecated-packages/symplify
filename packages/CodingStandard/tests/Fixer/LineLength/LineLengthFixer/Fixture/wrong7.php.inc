<?php

class SomeClass
{
    public function someFunction()
    {
        $this->someFunction(['foo', 'bar'], ['fooooooooooooooooo', 'baaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaar']);
    }
}

?>
-----
<?php

class SomeClass
{
    public function someFunction()
    {
        $this->someFunction(
            ['foo', 'bar'],
            ['fooooooooooooooooo', 'baaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaar']
        );
    }
}

?>
