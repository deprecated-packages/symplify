<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

if (file_exists($autoload = __DIR__.'/../../../autoload.php')) {
	require_once $autoload;
} else {
	require_once __DIR__.'/../vendor/autoload.php';
}
