<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Tests\TypeExtension\FuncCall\NativeFunctionDynamicFunctionReturnTypeExtension;

use PHPStan\Testing\TypeInferenceTestCase;

final class NativeFunctionDynamicFunctionReturnTypeExtensionTest extends TypeInferenceTestCase
{
    /**
     * @return iterable<string, mixed[]>
     */
    public function dataAsserts(): iterable
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
