<?php

/* @var int $variable */
$variable = 5;

/* @phpstan-var list<array{0: string}> $variable */
$variable = array(array('hello'));

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

/*
 * @phpstan-var $variable int
 */
$variable = 5;

?>
-----
<?php

/** @var int $variable */
$variable = 5;

/** @phpstan-var list<array{0: string}> $variable */
$variable = array(array('hello'));

/** @var int $variable */
$variable = 5;

/** @var int $variable */
$variable = 5;

/** @var int $variable */
$variable = 5;

/** @phpstan-var int $variable */
$variable = 5;

?>
