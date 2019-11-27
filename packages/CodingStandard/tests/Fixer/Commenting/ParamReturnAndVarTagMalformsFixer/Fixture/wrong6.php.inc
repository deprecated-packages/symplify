<?php

/**
 * @param string
 * @param string
 */
function another($class, $context)
{
}

/**
 * @param string $one
 * @param string
 * @param string $three
 */
function anotherFunction(string $one, string $two, string $three)
{
}

?>
-----
<?php

/**
 * @param string $class
 * @param string $context
 */
function another($class, $context)
{
}

/**
 * @param string $one
 * @param string $two
 * @param string $three
 */
function anotherFunction(string $one, string $two, string $three)
{
}

?>
