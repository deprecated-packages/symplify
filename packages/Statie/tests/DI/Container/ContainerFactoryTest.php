<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\DI\Container;

use Nette\DI\Container;
use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Symplify\Statie\DI\Container\ContainerFactory;

final class ContainerFactoryTest extends TestCase
{
    /**
     * @var string
     */
    private $sourceDirectory = __DIR__ . '/../../../source';

    protected function setUp()
    {
        FileSystem::createDir($this->sourceDirectory);
    }

    protected function tearDown()
    {
        FileSystem::delete($this->sourceDirectory);
    }

    public function testCreate()
    {
        $container = (new ContainerFactory)->create();
        $this->assertInstanceOf(Container::class, $container);
    }
}
