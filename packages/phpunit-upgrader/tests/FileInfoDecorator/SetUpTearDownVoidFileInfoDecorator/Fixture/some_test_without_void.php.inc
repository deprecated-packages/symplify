<?php

declare(strict_types=1);

namespace Symplify\PHPUnitUpgrader\Tests\FileInfoDecorator\SetUpTearDownVoidFileInfoDecorator\Fixture;

use PHPUnit\Framework\TestCase;

final class SomeTestWithoutVoid extends TestCase
{
    protected function setUp()
    {
    }
}

?>
-----
<?php

declare(strict_types=1);

namespace Symplify\PHPUnitUpgrader\Tests\FileInfoDecorator\SetUpTearDownVoidFileInfoDecorator\Fixture;

use PHPUnit\Framework\TestCase;

final class SomeTestWithoutVoid extends TestCase
{
    protected function setUp(): void
    {
    }
}

?>
