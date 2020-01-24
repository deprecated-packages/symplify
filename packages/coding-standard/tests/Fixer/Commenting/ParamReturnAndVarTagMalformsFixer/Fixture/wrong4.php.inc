<?php

/**
 * @param $instanceof ChildDefinition[]
 */
function someFunction($instanceof)
{
}

/**
 * @param $value
 *
 * @return $value The normalized array value
 */
function preNormalize($value)
{
}

/**
 * @param string $class The class the object should be
 * @param $context The denormalization context
 */
function another($class, $context)
{
}

?>
-----
<?php

/**
 * @param ChildDefinition[] $instanceof
 */
function someFunction($instanceof)
{
}

/**
 * @param $value
 *
 * @return The normalized array value
 */
function preNormalize($value)
{
}

/**
 * @param string $class The class the object should be
 * @param $context The denormalization context
 */
function another($class, $context)
{
}

?>
