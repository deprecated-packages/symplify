<?php

function someFunction()
{
    /** @var int */
    $value = 1000;
    /** @phpstan-var int */
    $value1 = 1000;
    /** @psalm-var int */
    $value2 = 1000;
}

?>
-----
<?php

function someFunction()
{
    /** @var int $value */
    $value = 1000;
    /** @phpstan-var int $value1 */
    $value1 = 1000;
    /** @psalm-var int $value2 */
    $value2 = 1000;
}

?>
