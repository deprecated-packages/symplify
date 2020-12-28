<?php

function someFunctionWithManyTypes()
{
    /** @var int|\stdClass */
    $value = 1000;
}

?>
-----
<?php

function someFunctionWithManyTypes()
{
    /** @var int|\stdClass $value */
    $value = 1000;
}

?>
