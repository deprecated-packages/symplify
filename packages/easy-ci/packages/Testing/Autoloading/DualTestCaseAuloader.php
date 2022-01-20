<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Testing\Autoloading;

use PHPUnit\Framework\TestCase;

/**
 * To ease detection of dual test case classes.
 */
final class DualTestCaseAuloader
{
    public function autoload(): void
    {
        if (! class_exists('PHPUnit_Framework_TestCase')) {
            class_alias(TestCase::class, 'PHPUnit_Framework_TestCase');
        } elseif (! class_exists(TestCase::class)) {
            class_alias('PHPUnit_Framework_TestCase', TestCase::class);
        }
    }
}
