<?php

declare(strict_types=1);

namespace Symplify\PHPUnitUpgrader\Tests\FileInfoDecorator\SetUpTearDownVoidFileInfoDecorator\Fixture;

use PHPUnit\Framework\TestCase;

final class SkipAlreadyVoid extends TestCase
{
    protected function setUp(): void
    {
    }
}
