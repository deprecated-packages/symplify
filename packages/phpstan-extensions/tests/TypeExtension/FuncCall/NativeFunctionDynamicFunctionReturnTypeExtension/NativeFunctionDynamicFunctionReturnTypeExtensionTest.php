<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Tests\TypeExtension\FuncCall\NativeFunctionDynamicFunctionReturnTypeExtension;

use Iterator;
use PHPStan\Testing\TypeInferenceTestCase;

final class NativeFunctionDynamicFunctionReturnTypeExtensionTest extends TypeInferenceTestCase
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
