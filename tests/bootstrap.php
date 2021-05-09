<?php

declare(strict_types=1);

use PHP_CodeSniffer\Util\Tokens;
use Tracy\Debugger;

// prefer local class over Rector partially scoped one, to avoid confussion in tests autoload
require_once __DIR__ . '/../packages/symfony-php-config/src/ValueObjectInliner.php';


require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/squizlabs/php_codesniffer/autoload.php';


// initalize token with INT type, otherwise php-cs-fixer and php-parser breaks
if (defined('T_MATCH') === false) {
    define('T_MATCH', 5000);
}

// required for PHP_CodeSniffer in packages/EasyCodingStandard/tests/*
if (! defined('PHP_CODESNIFFER_VERBOSITY')) {
    define('PHP_CODESNIFFER_VERBOSITY', 0);
    // initialize custom T_* token constants used by PHP_CodeSniffer parser
    new Tokens();
}

// absolute paths differ in monorepo and split packages
// e.g. /packagse/EasyCodingStandard/src (monorepo) => src (after monorepo)
// use this to find out where you are
define('SYMPLIFY_MONOREPO', true);

// to keep dumping of Nodes simple
Debugger::$maxDepth = 2;
