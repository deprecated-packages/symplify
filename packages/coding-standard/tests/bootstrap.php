<?php

declare(strict_types=1);

use PHP_CodeSniffer\Util\Tokens;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/squizlabs/php_codesniffer/autoload.php';

// initalize token with INT type, otherwise php-cs-fixer and php-parser breaks
// initalize token with INT type, otherwise php-cs-fixer and php-parser breaks
if (! defined('T_MATCH')) {
    define('T_MATCH', 5005);
}

if (! defined('T_READONLY')) {
    define('T_READONLY', 5010);
}

if (! defined('T_ENUM')) {
    define('T_ENUM', 5015);
}


// required for PHP_CodeSniffer in packages/EasyCodingStandard/tests/*
if (! defined('PHP_CODESNIFFER_VERBOSITY')) {
    define('PHP_CODESNIFFER_VERBOSITY', 0);
    // initialize custom T_* token constants used by PHP_CodeSniffer parser
    new Tokens();
}
