<?php

declare(strict_types=1);

namespace Symplify\PHPUnitUpgrader\Tests\FileInfoDecorator\AssertContainsInfoDecorator\Fixture;

final class SomeAssertContains
{
    public function test()
    {
        $this->assertContains('one', []);
        $this->assertContains('one', 'string value');
    }
}

?>
-----
<?php

declare(strict_types=1);

namespace Symplify\PHPUnitUpgrader\Tests\FileInfoDecorator\AssertContainsInfoDecorator\Fixture;

final class SomeAssertContains
{
    public function test()
    {
        $this->assertContains('one', []);
        $this->assertStringContainsString('one', 'string value');
    }
}

?>
