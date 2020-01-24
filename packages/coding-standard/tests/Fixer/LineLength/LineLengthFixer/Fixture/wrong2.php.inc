<?php

class SomeClass
{
    public function someFunction(
        SomeLongArgument $someLongArgument,
        AnotherLongArgument $anotherLongArgument
    ) {
    }

    public function someOtherFunction(
        SomeLongArgument $someLongArgument,
        AnotherLongArgument $anotherLongArgument,
        LongOneArgument $longOneArgument
    ) {
    }
}

?>
-----
<?php

class SomeClass
{
    public function someFunction(SomeLongArgument $someLongArgument, AnotherLongArgument $anotherLongArgument) {
    }

    public function someOtherFunction(
        SomeLongArgument $someLongArgument,
        AnotherLongArgument $anotherLongArgument,
        LongOneArgument $longOneArgument
    ) {
    }
}

?>
