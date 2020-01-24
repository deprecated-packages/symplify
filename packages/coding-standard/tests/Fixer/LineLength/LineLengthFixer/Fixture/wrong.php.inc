<?php

class SomeClass
{
    public function someFunction(SomeLongArgument $someLongArgument, AnotherLongArgument $anotherLongArgument, $passTheLimit)
    {
    }

    public function someFineFunction(SomeLongArgument $someLongArgumentFine, AnotherLongArgument $anotherLongArgumentTo)
    {
    }

    public function someLongFunction(SomeLongArgument $someLongArgumentNotFine, AnotherLongArgument $anotherLongArgument)
    {
    }
}

?>
-----
<?php

class SomeClass
{
    public function someFunction(
        SomeLongArgument $someLongArgument,
        AnotherLongArgument $anotherLongArgument,
        $passTheLimit
    )
    {
    }

    public function someFineFunction(SomeLongArgument $someLongArgumentFine, AnotherLongArgument $anotherLongArgumentTo)
    {
    }

    public function someLongFunction(
        SomeLongArgument $someLongArgumentNotFine,
        AnotherLongArgument $anotherLongArgument
    )
    {
    }
}

?>
