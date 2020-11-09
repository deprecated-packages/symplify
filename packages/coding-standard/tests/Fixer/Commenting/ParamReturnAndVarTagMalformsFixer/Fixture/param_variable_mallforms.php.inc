<?php

/**
 * @param $name string
 * @param string
 * @param string Some description
 */
function someFunction(string $name, string $surname, string $three)
{
}

?>
-----
<?php

/**
 * @param string $name
 * @param string $surname
 * @param string $three Some description
 */
function someFunction(string $name, string $surname, string $three)
{
}

?>
