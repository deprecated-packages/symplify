<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Testing\Autoloading;

/**
 * To ease detection of dual test case classes.
 */
final class DualTestCaseAuloader
{
    /**
     * @var string
     */
    private const UNDERSCORED_TEST_CASE_CLASS = 'PHPUnit_Framework_TestCase';

    public function autoload(): void
    {
        if (! class_exists(self::UNDERSCORED_TEST_CASE_CLASS)) {
            // alias new test case to old one
            class_alias('PHPUnit\Framework\TestCase', self::UNDERSCORED_TEST_CASE_CLASS);
            return;
        }

        if (! class_exists('PHPUnit\Framework\TestCase')) {
            // alias old test case to new one
            class_alias(self::UNDERSCORED_TEST_CASE_CLASS, 'PHPUnit\Framework\TestCase');
        }
    }
}
