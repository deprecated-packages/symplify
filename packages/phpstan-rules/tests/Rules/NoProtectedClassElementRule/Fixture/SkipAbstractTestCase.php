<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoProtectedClassElementRule\Fixture;

use PHPUnit\Framework\TestCase;

abstract class SkipAbstractTestCase extends TestCase
{
    protected function getService(string $type)
    {
    }
}
