<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\Latte;

use PHPUnit\Framework\TestCase;
use Symplify\Statie\Latte\DynamicStringLoader;

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

    /**
     * @expectedException \Exception
     */
    public function testGetContentOnMissing(): void
    {
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
            file_get_contents(__DIR__ . '/LatteDecoratorSource/default.latte')
        );

        return $loader;
    }
}
