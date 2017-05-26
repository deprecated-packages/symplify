<?php declare(strict_types=1);

namespace Symplify\Statie\FlatWhite\Tests\Latte;

use PHPUnit\Framework\TestCase;
use Symplify\Statie\FlatWhite\Latte\DynamicStringLoader;
use Throwable;

final class DynamicStringLoaderTest extends TestCase
{
    /**
     * @var DynamicStringLoader
     */
    private $stringLoader;

    protected function setUp(): void
    {
        $this->stringLoader = $this->createStringLoader();
    }

    public function testGetContentOnMissing(): void
    {
        $this->expectException(Throwable::class);
        $this->stringLoader->getContent('missing');
    }

    public function testIsExpired(): void
    {
        $this->assertFalse($this->stringLoader->isExpired('missing', 123));
    }

    private function createStringLoader(): DynamicStringLoader
    {
        $loader = new DynamicStringLoader;
        $loader->changeContent(
            'default',
            file_get_contents(__DIR__ . '/DynamicStringLoaderSource/default.latte')
        );

        return $loader;
    }
}
