<?php

/**
 * @param string[]|array $variable
 * @return array|mixed|mixed[]
 * @return mixed|array|mixed[]
 * @return mixed[]|array|mixed
 */
function someFunction($variable)
{
}

?>
-----
<?php

/**
 * @param string[] $variable
 * @return mixed|mixed[]
 * @return mixed|mixed[]
 * @return mixed[]|mixed
 */
function someFunction($variable)
{
}

?>
