<?php

/** @var Composer\Autoload\ClassLoader $classLoader */
$classLoader = require __DIR__ . '/../vendor/autoload.php';

Symplify\PHP7_CodeSniffer\Legacy\LegacyCompatibilityLayer::add();

$classLoaderDecorator = new Symplify\PHP7_CodeSniffer\Composer\ClassLoaderDecorator(
    new Symplify\PHP7_CodeSniffer\Standard\Finder\StandardFinder()
);
$classLoaderDecorator->decorate($classLoader);
