<?php declare(strict_types=1);

namespace Symplify\Statie\Latte\Tests\Loader;

use PHPUnit\Framework\TestCase;
use Symplify\Statie\Latte\Loader\ArrayLoader;
use Throwable;

final class ArrayLoaderTest extends TestCase
{
    /**
     * @var ArrayLoader
     */
    private $arrayLoader;

    protected function setUp(): void
    {
        $this->arrayLoader = $this->createStringLoader();
    }

    public function testGetContentOnMissing(): void
    {
        $this->expectException(Throwable::class);
        $this->arrayLoader->getContent('missing');
    }

    public function testIsExpired(): void
    {
        $this->assertFalse($this->arrayLoader->isExpired('missing', 123));
    }

    private function createStringLoader(): ArrayLoader
    {
        $loader = new ArrayLoader();
        $loader->changeContent('default', file_get_contents(__DIR__ . '/ArrayLoaderSource/default.latte'));

        return $loader;
    }
}
