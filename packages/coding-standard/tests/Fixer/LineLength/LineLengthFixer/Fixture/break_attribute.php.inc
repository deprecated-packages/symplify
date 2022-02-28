<?php

#[SomeObject($someLongArgument, $someLongArgument, $anotherLongArgument, $anotherLongArgument, $passTheLimit)]
class SomeClass
{

}

#[SomeObject($someLongArgument, $someLongArgument, $anotherLongArgument, $anotherLongArgument, $limits)]
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
    $passTheLimit
)]
class SomeClass
{

}

#[SomeObject($someLongArgument, $someLongArgument, $anotherLongArgument, $anotherLongArgument, $limits)]
class SomeClass
{

}
?>
