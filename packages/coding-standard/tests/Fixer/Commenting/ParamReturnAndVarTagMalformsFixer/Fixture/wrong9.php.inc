<?php

/**
 * @param argument
 * @param string argument2
 * @param ExceptionMessageFactoryInterface|null exceptionMessageFactory
 * @param string[] items
 */
function someFunction($argument, $argument2, $exceptionMessageFactory, $items)
{
}

?>
-----
<?php

/**
 * @param $argument
 * @param string $argument2
 * @param ExceptionMessageFactoryInterface|null $exceptionMessageFactory
 * @param string[] $items
 */
function someFunction($argument, $argument2, $exceptionMessageFactory, $items)
{
}

?>
