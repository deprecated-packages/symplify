<?php declare(strict_types=1);

namespace Symplify\Statie\FlatWhite\Tests\Latte;

use PHPUnit\Framework\TestCase;
use Symplify\Statie\FlatWhite\Latte\ArrayLoader;
use Throwable;

final class DynamicStringLoaderTest extends TestCase
{
    /**
     * @var ArrayLoader
     */
    private $dynamicStringLoader;

    protected function setUp(): void
    {
        $this->dynamicStringLoader = $this->createStringLoader();
    }

    public function testGetContentOnMissing(): void
    {
        $this->expectException(Throwable::class);
        $this->dynamicStringLoader->getContent('missing');
    }

    public function testIsExpired(): void
    {
        $this->assertFalse($this->dynamicStringLoader->isExpired('missing', 123));
    }

    private function createStringLoader(): ArrayLoader
    {
        $loader = new ArrayLoader();
        $loader->changeContent('default', file_get_contents(__DIR__ . '/DynamicStringLoaderSource/default.latte'));

        return $loader;
    }
}
