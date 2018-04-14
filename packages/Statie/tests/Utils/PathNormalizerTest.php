<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Utils;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\Statie\Utils\PathNormalizer;

final class PathNormalizerTest extends TestCase
{
    /**
     * @dataProvider provideDataForNormalize()
     */
    public function test(string $normalizedPath, string $path): void
    {
        $pathNormalizer = new PathNormalizer();

        $this->assertSame($normalizedPath, $pathNormalizer->normalize($path));
    }

    public function provideDataForNormalize(): Iterator
    {
        yield ['dir-one' . DIRECTORY_SEPARATOR . 'dir-two', 'dir-one\dir-two'];
        yield ['dir-one' . DIRECTORY_SEPARATOR . 'dir-two', 'dir-one/dir-two'];
    }
}
