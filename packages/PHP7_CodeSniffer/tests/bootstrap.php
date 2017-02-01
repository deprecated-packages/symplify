<?php declare(strict_types=1);

/** @var Composer\Autoload\ClassLoader $classLoader */
$classLoader = require __DIR__ . '/../vendor/autoload.php';

Symplify\PHP7_CodeSniffer\Legacy\LegacyCompatibilityLayer::add();
