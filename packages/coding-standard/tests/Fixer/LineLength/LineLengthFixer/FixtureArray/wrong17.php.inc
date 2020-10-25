<?php

class SomeClass
{
    public function someFunction()
    {
        yield [
            'too',
            'short'
        ];
    }
}

?>
-----
<?php

class SomeClass
{
    public function someFunction()
    {
        yield ['too', 'short'];
    }
}

?>
