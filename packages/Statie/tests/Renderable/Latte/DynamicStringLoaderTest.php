<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Renderable\Latte;

use PHPUnit\Framework\TestCase;
use Symplify\Statie\Renderable\Latte\DynamicStringLoader;

final class DynamicStringLoaderTest extends TestCase
{
    /**
     * @var DynamicStringLoader
     */
    private $stringLoader;

    protected function setUp()
    {
        $this->stringLoader = $this->createStringLoader();
    }

    /**
     * @expectedException \Exception
     */
    public function testGetContentOnMissing()
    {
        $this->stringLoader->getContent('missing');
    }

    public function testIsExpired()
    {
        $this->assertFalse($this->stringLoader->isExpired('missing', 123));
    }

    private function createStringLoader(): DynamicStringLoader
    {
        $loader = new DynamicStringLoader;
        $loader->addTemplate(
            'default',
            file_get_contents(__DIR__ . '/LatteDecoratorSource/default.latte')
        );

        return $loader;
    }
}
