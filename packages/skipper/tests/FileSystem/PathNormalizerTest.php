<?php

declare(strict_types=1);

namespace Symplify\Skipper\Tests\FileSystem;

use Iterator;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\Skipper\FileSystem\PathNormalizer;
use Symplify\Skipper\HttpKernel\SkipperKernel;

final class PathNormalizerTest extends AbstractKernelTestCase
{
    /**
     * @var PathNormalizer
     */
    private $pathNormalizer;

    protected function setUp(): void
    {
        $this->bootKernel(SkipperKernel::class);
        $this->pathNormalizer = self::$container->get(PathNormalizer::class);
    }

    /**
     * @dataProvider providePaths
     */
    public function testPaths(string $path, string $expected): void
    {
        $this->assertEquals(
            $expected,
            $this->pathNormalizer->normalizeForFnmatch($path)
        );
    }

    public function providePaths(): Iterator
    {
        yield ['path/with/no/asterisk', 'path/with/no/asterisk'];
        yield ['*path/with/asterisk/begin', '*path/with/asterisk/begin*'];
        yield ['path/with/asterisk/end*', '*path/with/asterisk/end*'];
        yield ['*path/with/asterisk/begin/and/end*', '*path/with/asterisk/begin/and/end*'];
        yield [ __DIR__ . '/Fixture/path/with/../in/it', __DIR__ . '/Fixture/path/in/it'];
        yield [ __DIR__ . '/Fixture/path/with/../../in/it', __DIR__ . '/Fixture/in/it'];
    }
}
