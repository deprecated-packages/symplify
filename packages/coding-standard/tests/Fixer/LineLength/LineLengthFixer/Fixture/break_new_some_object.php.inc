<?php

$someObject = new SomeObject($someLongArgument, $someLongArgument, $anotherLongArgument, $anotherLongArgument, $passTheLimit);

$someObject = new SomeObject($someLongArgument, $someLongArgument, $anotherLongArgument, $anotherLongArgument, $limits);

?>
-----
<?php

$someObject = new SomeObject(
    $someLongArgument,
    $someLongArgument,
    $anotherLongArgument,
    $anotherLongArgument,
    $passTheLimit
);

$someObject = new SomeObject($someLongArgument, $someLongArgument, $anotherLongArgument, $anotherLongArgument, $limits);

?>
