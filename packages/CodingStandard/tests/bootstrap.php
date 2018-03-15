<?php declare(strict_types=1);

use PhpCsFixer\Tests\Test\Constraint\SameStringsConstraint;
use Symplify\CodingStandard\Tests\PHPUnit\Constraint\FixedSameStringsConstraint;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/squizlabs/php_codesniffer/autoload.php';

// fix from https://github.com/FriendsOfPHP/PHP-CS-Fixer/pull/3592
class_alias(FixedSameStringsConstraint::class, SameStringsConstraint::class);
