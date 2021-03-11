<?php

/**
 * @param $a string
 * @param $b string|null
 * @return string
 */
public function test($a, string $b = null): string
{
    return $a . $b;
}

?>
-----
<?php

/**
 * @param string $a
 * @param string|null $b
 * @return string
 */
public function test($a, string $b = null): string
{
    return $a . $b;
}

?>
