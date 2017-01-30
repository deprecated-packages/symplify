<?php declare(strict_types=1);

if (file_exists($autoload = __DIR__.'/../../../autoload.php')) {
	require_once $autoload;
} else {
	require_once __DIR__.'/../vendor/autoload.php';
}
