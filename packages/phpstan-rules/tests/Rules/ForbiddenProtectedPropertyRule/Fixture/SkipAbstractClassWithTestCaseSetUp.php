<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenProtectedPropertyRule\Fixture;

use PHPUnit\Framework\TestCase;

abstract class SkipAbstractClassWithTestCaseSetUp extends TestCase
{
    protected $config;

    protected function setUp(): void
    {
        $this->config = 100;
    }
}
