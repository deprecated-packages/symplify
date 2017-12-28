<?php

$x = in_array("foo", array('a', 'b'));
$x = in_array("foo", ['b', 'c']);
if (in_array($str, ['a', 'b', 'c'])) {}
$x = in_array("foo", array('a', 'b'), true);
$x = in_array("foo", ['b', 'c'], true);
if (in_array($str, ['a', 'b', 'c'], true)) {}
$x = in_array("foo", array('a', 'b'), false);
$x = in_array("foo", ['b', 'c'], false);
if (in_array($str, ['a', 'b', 'c'], false)) {}
