<?php

function someFunction()
{
    /** @var int */
    $value = 1000;
}

?>
-----
<?php

function someFunction()
{
    /** @var int $value */
    $value = 1000;
}

?>
