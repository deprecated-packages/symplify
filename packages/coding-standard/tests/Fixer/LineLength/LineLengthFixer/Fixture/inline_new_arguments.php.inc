<?php

$someObject = new SomeClass(
    $someLongArgument,
    $anotherLongArgument
);

?>
-----
<?php

$someObject = new SomeClass($someLongArgument, $anotherLongArgument);

?>
