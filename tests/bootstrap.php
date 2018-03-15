<?php declare(strict_types=1);

use PHP_CodeSniffer\Util\Tokens;
use PhpCsFixer\Tests\Test\Constraint\SameStringsConstraint;
use Symplify\CodingStandard\Tests\PHPUnit\Constraint\FixedSameStringsConstraint;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/squizlabs/php_codesniffer/autoload.php';

// required for PHP_CodeSniffer in packages/EasyCodingStandard/tests/*
if (! defined('PHP_CODESNIFFER_VERBOSITY')) {
    define('PHP_CODESNIFFER_VERBOSITY', 0);
    // initialize custom T_* token constants used by PHP_CodeSniffer parser
    new Tokens();
}

// fix from https://github.com/FriendsOfPHP/PHP-CS-Fixer/pull/3592
class_alias(FixedSameStringsConstraint::class, SameStringsConstraint::class);
