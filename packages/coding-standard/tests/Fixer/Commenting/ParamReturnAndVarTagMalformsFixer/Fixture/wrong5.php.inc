<?php

/* @var int $variable */
$variable = 5;

/** @var $variable int */
$variable = 5;

/**
 * @var $variable int
 */
$variable = 5;

/*
 * @var $variable int
 */
$variable = 5;

?>
-----
<?php

/** @var int $variable */
$variable = 5;

/** @var int $variable */
$variable = 5;

/** @var int $variable */
$variable = 5;

/** @var int $variable */
$variable = 5;

?>
