<?php

/**
 * @return int $value
 */
function function1(): int
{
}

/**
 * @return int $value This is the value
 */
function function2(): int
{
}

/**
 * @return $value This is the value
 */
function function3(): int
{
}

/**
 * @return $this
 */
function returnThis()
{
}

/**
 * @return self|null A clone of $this or null if the key is not set
 */
function returnThisDescription()
{
}

/**
 * @return self|null A clone of $value or null if the key is not set
 */
function returnValuesDescription()
{
}

?>
-----
<?php

/**
 * @return int
 */
function function1(): int
{
}

/**
 * @return int This is the value
 */
function function2(): int
{
}

/**
 * @return This is the value
 */
function function3(): int
{
}

/**
 * @return $this
 */
function returnThis()
{
}

/**
 * @return self|null A clone of $this or null if the key is not set
 */
function returnThisDescription()
{
}

/**
 * @return self|null A clone of $value or null if the key is not set
 */
function returnValuesDescription()
{
}

?>
