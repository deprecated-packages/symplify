<?php

#[SomeObject($someLongArgument, $someLongArgument, $anotherLongArgument, $anotherLongArgument, $passTheLimit, $passTheLimit, $passTheLimit)]
class SomeClass
{

}

?>
-----
<?php

#[SomeObject(
    $someLongArgument,
    $someLongArgument,
    $anotherLongArgument,
    $anotherLongArgument,
    $passTheLimit,
    $passTheLimit,
    $passTheLimit
)]
class SomeClass
{

}

?>
