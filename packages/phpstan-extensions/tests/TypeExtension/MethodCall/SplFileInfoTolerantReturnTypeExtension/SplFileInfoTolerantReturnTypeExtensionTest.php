<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Tests\TypeExtension\MethodCall\SplFileInfoTolerantReturnTypeExtension;

use Iterator;
use PHPStan\Testing\TypeInferenceTestCase;

final class SplFileInfoTolerantReturnTypeExtensionTest extends TypeInferenceTestCase
{
    public function dataAsserts(): Iterator
    {
        yield from $this->gatherAssertTypes(__DIR__ . '/data/fixture.php');
    }

    /**
     * @dataProvider dataAsserts()
     */
    public function testAsserts(string $assertType, string $file, mixed ...$args): void
    {
        $this->assertFileAsserts($assertType, $file, ...$args);
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/config/config.neon'];
    }
}
